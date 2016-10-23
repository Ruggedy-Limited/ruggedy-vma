<?php

namespace App\Services\Parsers;

use App\Contracts\ParsesXmlFiles;
use App\Entities\Asset;
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
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => Vulnerability::class,
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

            'request' => new Collection([
                'captureCDataField' => new Collection([
                    Vulnerability::class,
                    'setHttpRawRequest',
                ]),
            ]),

            'response' => new Collection([
                'captureCDataField' => new Collection([
                    Vulnerability::class,
                    'setHttpRawResponse',
                ]),
            ]),

            'location' => new Collection([
                'captureCDataField' => new Collection([
                    Vulnerability::class,
                    'setHttpUri',
                ]),
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
            'issues' => 'flushDoctrineUnitOfWork',
        ]);
    }

    /**
     * Extract a CData field and set the value on the model, optionally appending the value
     *
     * @param string $entityClass
     * @param string $setter
     * @param string $heading
     * @param bool $append
     */
    protected function captureCDataField(
        string $entityClass, string $setter, string $heading = '', bool $append = false
    )
    {
        // Check if the base64 flag is set on the node
        $isBase64 = $this->checkForBase64Encoding();

        // Move into the CData node
        $this->parser->read();

        // Exit early is this is not a CData field
        if ($this->parser->nodeType !== XMLReader::CDATA) {
            return;
        }

        // Wrap the heading in <h3></h3> tags
        if (!empty($heading)) {
            $heading = '<h3>' . $heading . '</h3>' . PHP_EOL;
        }

        // Get the entity and validate it and the setter method
        $entity = $this->entities->get($entityClass);
        if (empty($entity) || !method_exists($entity, $setter)) {
            return;
        }

        $value = $isBase64 ? base64_decode($this->parser->value) : $this->parser->value;
        if (empty($value)) {
            return;
        }

        // When the append flag is not set, set the heading and node contents
        if (!$append) {
            $entity->$setter(
                $heading . $value
            );

            return;
        }

        // When the append flag is set, append the heading and node contents to the the value that exists
        $getter = 'g' . substr($setter, 1);
        $entity->$setter(
            $entity->$getter() . PHP_EOL . $heading . $value
        );
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
     * Check if the value is base
     *
     * @return bool
     */
    protected function checkForBase64Encoding(): bool
    {
        return $this->parser->getAttribute('base64') === 'true';
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