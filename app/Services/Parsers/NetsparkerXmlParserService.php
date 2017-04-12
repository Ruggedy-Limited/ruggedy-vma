<?php

namespace App\Services\Parsers;

use App\Entities\Asset;
use App\Entities\Vulnerability;
use App\Entities\VulnerabilityReferenceCode;
use App\Entities\Workspace;
use App\Repositories\AssetRepository;
use App\Repositories\FileRepository;
use App\Services\JsonLogService;
use Doctrine\ORM\EntityManager;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Validation\Factory;
use League\Tactician\CommandBus;
use XMLReader;

class NetsparkerXmlParserService extends AbstractXmlParserService
{
    /**
     * NetsparkerXmlParserService constructor.
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
        $this->fileToSchemaMapping = collect([
            'target.url' => collect([
                'setHostname' => collect([
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS => Asset::class,
                    parent::MAP_ATTRIBUTE_VALIDATION => [
                        'filled',
                        'regex:' . Asset::REGEX_HOSTNAME
                    ],
                ]),
            ]),
            'vulnerability.url' => collect([
                'setHttpUri' => collect([
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS => Vulnerability::class,
                    parent::MAP_ATTRIBUTE_VALIDATION   => 'filled|url',
                ]),
            ]),
            'type' => collect([
                'setName' => collect([
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS => Vulnerability::class,
                    parent::MAP_ATTRIBUTE_VALIDATION   => 'filled',
                ]),
                'setIdFromScanner' => collect([
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS => Vulnerability::class,
                    parent::MAP_ATTRIBUTE_VALIDATION   => 'filled',
                ]),
            ]),
            'severity' => collect([
                'setSeverity' => collect([
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS => Vulnerability::class,
                    parent::MAP_ATTRIBUTE_VALIDATION   => 'filled|in:Critical,High,Medium,Low,Information',
                ]),
            ]),
            'extrainformation' => collect([
                'setGenericOutput' => collect([
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS => Vulnerability::class,
                    parent::MAP_ATTRIBUTE_VALIDATION   => 'filled',
                ])
            ]),
            'vulnerableparameter' => collect([
                'setHttpTestParameter' => collect([
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS => Vulnerability::class,
                    parent::MAP_ATTRIBUTE_VALIDATION   => 'filled',
                ]),
            ]),
            'vulnerableparametervalue' => collect([
                'setHttpAttackPattern' => collect([
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS => Vulnerability::class,
                    parent::MAP_ATTRIBUTE_VALIDATION   => 'filled',
                ]),
            ]),
            'vulnerableparametertype' => collect([
                'setHttpMethod' => collect([
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS => Vulnerability::class,
                    parent::MAP_ATTRIBUTE_VALIDATION   => 'filled|in:GET,HEAD,POST,PUT,OPTIONS,CONNECT,TRACE,DELETE',
                ]),
            ]),
        ]);

        // Pre-processing method map
        $this->nodePreprocessingMap = collect([
            'target' => collect([
                'initialiseNewEntity' => collect([
                    Asset::class,
                ]),
            ]),
            'vulnerability' => collect([
                'initialiseNewEntity' => collect([
                    Vulnerability::class,
                ]),
            ]),
            'rawrequest' => collect([
                'captureCDataField' => collect([
                    Vulnerability::class,
                    'setHttpRawRequest',
                ]),
            ]),
            'rawresponse' => collect([
                'captureCDataField' => collect([
                    Vulnerability::class,
                    'setHttpRawResponse',
                ]),
            ]),
            'classification' => 'extractVulnerabilityReferences',
        ]);

        // Post-processing method map
        $this->nodePostProcessingMap = collect([
            'target' => collect([
                'persistPopulatedEntity' => collect([
                    Asset::class,
                    [
                        Asset::HOSTNAME      => null,
                        Asset::IP_ADDRESS_V4 => null,
                    ],
                    Workspace::class
                ]),
            ]),
            'vulnerability' => collect([
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
            'netsparker' => 'flushDoctrineUnitOfWork',
        ]);
    }

    /**
     * Extract the VulnerabilityReferenceCodes and add them to the parent Vulnerability entity
     */
    protected function extractVulnerabilityReferences()
    {
        // Iterate until we hit the ending classification tag
        do {
            $this->parser->read();

            if ($this->parser->nodeType !== XMLReader::ELEMENT) {
                continue;
            }

            // Create and populate a new VulnerabilityReferenceCode entity
            $this->initialiseNewEntity(VulnerabilityReferenceCode::class);
            $this->entities->get(VulnerabilityReferenceCode::class)
                ->setReferenceType($this->parser->name)
                ->setValue($this->parser->readInnerXml());

            // Add the populated VulnerabilityReferenceCode entity to the parent Vulnerability, but don't persist. They
            // will be persisted by a cascade persist operation when the parent Vulnerability is persisted
            $this->persistPopulatedEntity(
                VulnerabilityReferenceCode::class,
                [
                    VulnerabilityReferenceCode::REFERENCE_TYPE => null,
                    VulnerabilityReferenceCode::VALUE => null
                ],
                Vulnerability::class,
                false,
                false
            );
        } while ($this->parser->name != 'classification');
    }

    /**
     * @inheritdoc
     * @return string
     */
    protected function getBaseTagName()
    {
        return 'netsparker';
    }

    /**
     * @inheritdoc
     * @return string
     */
    public function getStoragePath(): string
    {
        return storage_path('scans/xml/netsparker');
    }

    /**
     * @inheritdoc
     */
    public function getLogFilename(): string
    {
        return 'netsparker-xml-parser.json.log';
    }
}