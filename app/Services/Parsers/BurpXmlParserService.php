<?php

namespace App\Services\Parsers;

use App\Contracts\ParsesXmlFiles;
use App\Entities\Asset;
use App\Entities\VulnerabilityHttpData;
use App\Entities\VulnerabilityReferenceCode;
use App\Entities\Workspace;
use App\Entities\Vulnerability;
use App\Repositories\AssetRepository;
use App\Repositories\FileRepository;
use App\Services\JsonLogService;
use Doctrine\ORM\EntityManager;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Validation\Factory;
use League\Tactician\CommandBus;
use Monolog\Logger;
use XMLReader;

class BurpXmlParserService extends AbstractXmlParserService implements ParsesXmlFiles
{
    protected $location;

    /**
     * BurpXmlParserService constructor.
     *
     * @param XMLReader $parser
     * @param Filesystem $fileSystem
     * @param Factory $validatorFactory
     * @param AssetRepository $assetRepository
     * @param FileRepository $fileRepository
     * @param EntityManager $em
     * @param JsonLogService $logger
     * @param CommandBus $commandBus
     */
    public function __construct(
        XMLReader $parser, Filesystem $fileSystem, Factory $validatorFactory, AssetRepository $assetRepository,
        FileRepository $fileRepository, EntityManager $em, JsonLogService $logger, CommandBus $commandBus
    )
    {
        parent::__construct(
            $parser, $fileSystem, $validatorFactory, $assetRepository, $fileRepository, $em, $logger, $commandBus
        );

        // Create the mappings to use when parsing the NMAP XML output
        $this->fileToSchemaMapping = new Collection([
            'host' => new Collection([

                'setIpAddressV4' => new Collection([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'ip',
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => Asset::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => FILTER_FLAG_IPV4,
                ]),

                'setHostname' => new Collection([
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS => Asset::class,
                    parent::MAP_ATTRIBUTE_VALIDATION   => [
                        'filled',
                        'regex:' . Asset::REGEX_HOSTNAME
                    ]
                ]),
            ]),

            'type' => new Collection([
                'setIdFromScanner' => new Collection([
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS => Vulnerability::class,
                    parent::MAP_ATTRIBUTE_VALIDATION   => 'filled',
                ]),
            ]),

            'name' => new Collection([
                'setName' => new Collection([
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS => Vulnerability::class,
                    parent::MAP_ATTRIBUTE_VALIDATION   => 'filled',
                ]),
            ]),

            'severity' => new Collection([
                'setSeverity' => new Collection([
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS => Vulnerability::class,
                    parent::MAP_ATTRIBUTE_VALIDATION   => 'filled|in:Critical,High,Medium,Low,Information',
                ]),
            ]),


            'request' => new Collection([
                'setHttpMethod' => new Collection([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'method',
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => VulnerabilityHttpData::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled|in:GET,HEAD,POST,PUT,OPTIONS,CONNECT,TRACE,DELETE'
                ]),
            ]),
        ]);

        $this->nodePreprocessingMap = collect([
            'issue'           => collect([
                'initialiseNewEntity' => collect([
                    Vulnerability::class,
                ]),
            ]),

            'host'            => collect([
                'initialiseNewEntity' => collect([
                    Asset::class,
                ]),
            ]),

            'issueBackground' => new Collection([
                'captureCDataField' => new Collection([
                    Vulnerability::class,
                    'setDescription',
                    'Issue Background',
                ]),
            ]),

            'issueDetail' => new Collection([
                'captureCDataField' => new Collection([
                    Vulnerability::class,
                    'setDescription',
                    'Issue Detail',
                    true,
                ]),
            ]),

            'remediationBackground' => new Collection([
                'captureCDataField' => new Collection([
                    Vulnerability::class,
                    'setSolution',
                    'Remediation Background'
                ]),
            ]),

            'remediationDetail' => new Collection([
                'captureCDataField' => new Collection([
                    Vulnerability::class,
                    'setSolution',
                    'Remediation Detail',
                    true,
                ]),
            ]),

            'requestresponse' => collect([
                'initialiseNewEntity' => collect([
                    VulnerabilityHttpData::class,
                ]),
            ]),

            'request' => new Collection([
                'captureCDataField' => new Collection([
                    VulnerabilityHttpData::class,
                    'setHttpRawRequest',
                ]),
            ]),

            'response' => new Collection([
                'captureCDataField' => new Collection([
                    VulnerabilityHttpData::class,
                    'setHttpRawResponse',
                ]),
            ]),

            'location' => new Collection([
                'storeTemporaryLocation',
            ]),

            'references' => 'addReferences',
        ]);

        $this->nodePostProcessingMap = collect([
            'issue'  => collect([
                'persistPopulatedEntity' => collect([
                    Vulnerability::class,
                    [
                        Vulnerability::ID_FROM_SCANNER => null,
                        Vulnerability::NAME            => null,
                        Vulnerability::FILE            => null,
                    ],
                    Asset::class,
                ]),
            ]),
            'host'   => collect([
                'persistPopulatedEntity' => collect([
                    Asset::class,
                    [
                        Asset::HOSTNAME      => null,
                        Asset::IP_ADDRESS_V4 => null,
                    ],
                    Workspace::class,
                ]),
            ]),
            'requestresponse' => collect([
                'setLocation',
                'persistPopulatedEntity' => collect([
                    VulnerabilityHttpData::class,
                    [],
                    Vulnerability::class,
                    false,
                    false
                ]),
            ]),
            'issues' => 'flushDoctrineUnitOfWork',
        ]);
    }

    /**
     * Add online references for the current Vulnerability
     */
    protected function addReferences()
    {
        $this->parser->read();
        $html = $this->parser->expand();
        if (empty($html) || !($html instanceof \DOMCdataSection)) {
            $this->logger->log(Logger::WARNING, "Did not get a DOMCdataSection node when parsing references", [
                'nodeName' => $this->parser->name,
                'nodeType' => $this->parser->nodeType,
                'type'     => is_object($html) ? get_class($html) : 'Not an object',
            ]);

            return;
        }

        // Match all the URLs in the HTML
        preg_match_all('/<a href="(.*)">/', $html->nodeValue, $urls);
        if (empty($urls[1]) || !is_array($urls[1])) {
            $this->logger->log(Logger::WARNING, "No URLs matched in references", [
                'nodeName'   => $this->parser->name,
                'nodeType'   => $this->parser->nodeType,
                'type'       => is_object($html) ? get_class($html) : 'Not an object',
                'urlMatches' => $urls ?? 'No matches',
            ]);

            return;
        }

        // Convert URLs to a Collection and iterate over each url to create and persist a new VulnerabilityReferenceCode
        $urls = collect($urls[1]);
        $urls->each(function ($url) {
            // Create a new VulnerabilityReferenceCode and add it to the current Vulnerability
            $this->initialiseNewEntity(VulnerabilityReferenceCode::class);

            // Set values on the new entity
            $this->setValueOnEntity($url, 'setValue', VulnerabilityReferenceCode::class);
            $this->setValueOnEntity(
                VulnerabilityReferenceCode::REF_TYPE_ONLINE_OTHER,
                'setReferenceType',
                VulnerabilityReferenceCode::class
            );

            // Persist the populated entity
            $this->persistPopulatedEntity(
                VulnerabilityReferenceCode::class,
                [
                    VulnerabilityReferenceCode::REFERENCE_TYPE => null,
                    VulnerabilityReferenceCode::VALUE          => null,
                    VulnerabilityReferenceCode::VULNERABILITY  => null,
                ],
                Vulnerability::class,
                false,
                false
            );
        });
    }

    /**
     * Store the location value for this issue.
     */
    protected function storeTemporaryLocation()
    {
        // Move into the CData node
        $this->parser->read();

        // Exit early is this is not a CData field
        if ($this->parser->nodeType !== XMLReader::CDATA) {
            return;
        }

        // Make sure the current value in the parser is not empty
        $value = $this->parser->value;
        if (!isset($value)) {
            return;
        }

        // Set the location property
        $this->location = $value;
    }

    /**
     * Set the location on the VulnerabilityHttpData entity.
     */
    protected function setLocation()
    {
        // Get the VulnerabilityHttpData entity from the entity Collection and make sure there is an entity there and
        // that the location property is set on this service
        $entity = $this->entities->get(VulnerabilityHttpData::class);
        if (!isset($this->location, $entity)) {
            return;
        }

        $entity->setHttpUri($this->location);
    }

    /**
     * @inheritdoc
     */
    protected function getBaseTagName()
    {
        return 'issue';
    }

    /**
     * @inheritdoc
     */
    public function getLogFilename(): string
    {
        return 'burp-xml-parser.json.log';
    }

    /**
     * @inheritdoc
     */
    public function getStoragePath(): string
    {
        return storage_path('scans/xml/burp');
    }
}