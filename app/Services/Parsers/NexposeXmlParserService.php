<?php

namespace App\Services\Parsers;

use App\Contracts\ParsesXmlFiles;
use App\Entities\Asset;
use App\Entities\Exploit;
use App\Entities\OpenPort;
use App\Entities\SoftwareInformation;
use App\Entities\Vulnerability;
use App\Entities\VulnerabilityReferenceCode;
use App\Entities\Workspace;
use App\Repositories\AssetRepository;
use App\Repositories\FileRepository;
use App\Services\JsonLogService;
use Doctrine\ORM\EntityManager;
use Illuminate\Support\Collection;
use Illuminate\Validation\Factory;
use Illuminate\Filesystem\Filesystem;
use League\Tactician\CommandBus;
use XMLReader;

class NexposeXmlParserService extends AbstractXmlParserService implements ParsesXmlFiles
{
    const SOFTWARE_FINGERPRINTS = 'software_fingerprints';

    /** Nexpose XML node names */
    const XML_NODE_FINGERPRINT = 'fingerprint';

    /** Nexpose XML node attribute names */
    const XML_ATTRIBUTE_PORT      = 'port';
    const XML_ATTRIBUTE_CERTAINTY = 'certainty';
    const XML_ATTRIBUTE_VENDOR    = 'vendor';
    const XML_ATTRIBUTE_PRODUCT   = 'product';
    const XML_ATTRIBUTE_VERSION   = 'version';

    /** @var CommandBus */
    protected $bus;

    /** @var Collection */
    protected $genericOutput;

    /**
     * NexposeXmlParserService constructor.
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
            'node' => collect([
                'setIpAddressV4' => collect([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'address',
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => Asset::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => FILTER_FLAG_IPV4,
                ]),
                'setMacAddress' => collect([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'hardware-address',
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => Asset::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => [
                        'filled',
                        "regex:%[A-Z0-9]{12}%"
                    ],
                ]),
            ]),
            'names.name' => collect([
                'setHostname' => collect([
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS => Asset::class,
                    parent::MAP_ATTRIBUTE_VALIDATION => [
                        'filled',
                        'regex:' . Asset::REGEX_HOSTNAME
                    ],
                ]),
                'setNetbios' => collect([
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS => Asset::class,
                    parent::MAP_ATTRIBUTE_VALIDATION => [
                        'filled',
                        'regex:' . Asset::REGEX_NETBIOS_NAME
                    ],
                ]),
            ]),
            'os' => collect([
                'setVendor' => collect([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'family',
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => Asset::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => collect([
                        parent::MAP_ATTRIBUTE_MAIN_VALIDATION => [
                            'filled',
                            'regex:' . Asset::getValidVendorsRegex(),
                        ],
                        parent::MAP_ATTRIBUTE_RELATED_VALIDATION => collect([
                            self::XML_ATTRIBUTE_CERTAINTY => 'filled|in:1.00',
                        ]),
                    ]),
                ]),
                /*'setOsFamily' => collect([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'family',
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => Asset::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => collect([
                        parent::MAP_ATTRIBUTE_MAIN_VALIDATION => [
                            'filled',
                            'regex:' . Asset::REGEX_OS_VERSION,
                        ],
                        parent::MAP_ATTRIBUTE_RELATED_VALIDATION => collect([
                            self::XML_ATTRIBUTE_CERTAINTY => 'filled|in:1.00',
                        ]),
                    ]),
                ]),*/
                'setOsVersion' => collect([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'version',
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => Asset::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => collect([
                        parent::MAP_ATTRIBUTE_MAIN_VALIDATION => 'filled',
                        parent::MAP_ATTRIBUTE_RELATED_VALIDATION => collect([
                            self::XML_ATTRIBUTE_CERTAINTY => 'filled|in:1.00',
                        ]),
                    ]),
                ]),
                /*'setOsProduct' => collect([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'product',
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => Asset::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => collect([
                        parent::MAP_ATTRIBUTE_MAIN_VALIDATION => 'filled',
                    ]),
                ]),*/
            ]),
            'software.fingerprint' => collect([
                'setName' => collect([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'product',
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => SoftwareInformation::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled'
                ]),
                'setVersion'  => collect([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'version',
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => SoftwareInformation::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled'
                ]),
                'setVendor'  => collect([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'vendor',
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => SoftwareInformation::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled'
                ]),
            ]),
            'endpoint' => collect([
                'setProtocol' => collect([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'protocol',
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => OpenPort::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled'
                ]),
                'setNumber' => collect([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'port',
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => OpenPort::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled|int'
                ]),
            ]),
            'service' => collect([
                'setServiceName' => collect([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'name',
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => OpenPort::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled'
                ]),
            ]),
            'fingerprints.fingerprint' => collect([
                'setServiceProduct' => collect([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'product',
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => OpenPort::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled',
                ]),
            ]),
            'configuration'      => collect([
                'setServiceExtraInfo' => collect([
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => OpenPort::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled',
                ]),
            ]),
            'config'             => collect([
                'setServiceBanner' => collect([
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => OpenPort::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => collect([
                        parent::MAP_ATTRIBUTE_MAIN_VALIDATION => 'filled',
                        parent::MAP_ATTRIBUTE_RELATED_VALIDATION => collect([
                             'name' => [
                                 'filled',
                                 'regex:/banner/',
                             ],
                        ]),
                    ]),
                ]),
            ]),
            'vulnerability'   => collect([
                'setIdFromScanner' => collect([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'id',
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => Vulnerability::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled',
                ]),
                'setName'           => collect([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'title',
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => Vulnerability::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled',
                ]),
                'setSeverity'       => collect([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'severity',
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => Vulnerability::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => [
                        'filled',
                        'regex:/^\d*(\.\d{1,2})?$/',
                    ],
                ]),
                'setPciSeverity'    => collect([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'pciSeverity',
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => Vulnerability::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => [
                        'filled',
                        'regex:/^\d*(\.\d{1,2})?$/',
                    ],
                ]),
                'setCvssScore'      => collect([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'cvssScore',
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => Vulnerability::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => [
                        'filled',
                        'regex:/^\d*(\.\d{1,2})?$/',
                    ],
                ]),
                'setPublishedDateFromScanner' => collect([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'published',
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => Vulnerability::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled',
                ]),
                'setModifiedDateFromScanner' => collect([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'modified',
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => Vulnerability::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled',
                ]),
            ]),
            'malware'           => collect([
                'setMalwareDescription' => collect([
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => Vulnerability::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled',
                ]),
            ]),
            'exploit'          => collect([
                'setTitle' => collect([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'title',
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => Exploit::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled',
                ]),
                'setUrlReference' => collect([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'link',
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => Exploit::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled|url',
                ]),
                'setSkillLevel' => collect([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'skillLevel',
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => Exploit::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled|in:Novice,Intermediate,Expert',
                ]),
            ]),
            'description'       => collect([
                'setDescription' => collect([
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => Vulnerability::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled',
                ]),
            ]),
            'solution'          => collect([
                'setSolution' => collect([
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => Vulnerability::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled',
                ]),
            ]),
            'reference'         => collect([
                'setReferenceType' => collect([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'source',
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => VulnerabilityReferenceCode::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled',
                ]),
                'setValue'      => collect([
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => VulnerabilityReferenceCode::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled',
                ]),
            ]),
        ]);

        $vulnerabilityPreProcessing = collect([
            Vulnerability::class,
        ]);

        // Pre-processing method map
        $this->nodePreprocessingMap = collect([
            'node'        => collect([
                'initialiseNewEntity' => collect([
                    Asset::class,
                ]),
            ]),
            'software.fingerprint' => collect([
                'initialiseNewEntity' => collect([
                    SoftwareInformation::class,
                ]),
            ]),
            'endpoint'        => collect([
                'initialiseNewEntity' => collect([
                    OpenPort::class,
                ]),
            ]),
            'test'        => collect([
                'storeTemporaryRawData' => collect(['id', 'genericOutput']),
            ]),
            'vulnerability' => collect([
                'initialiseNewEntity' => $vulnerabilityPreProcessing,
            ]),
            'exploit'      => collect([
                'initialiseNewEntity' => collect([
                    Exploit::class,
                ]),
            ]),
            'reference'     => collect([
                'initialiseNewEntity' => collect([
                    VulnerabilityReferenceCode::class,
                ]),
            ]),
        ]);

        // Post-processing method map
        $this->nodePostProcessingMap = collect([
            'node'              => 'flushDoctrineUnitOfWork',
            'node.fingerprints' => collect([
                'persistPopulatedEntity' => collect([
                    Asset::class,
                    [
                        Asset::HOSTNAME      => null,
                        Asset::IP_ADDRESS_V4 => null,
                    ],
                    Workspace::class
                ]),
            ]),
            'software.fingerprint'  => collect([
                'persistPopulatedEntity' => collect([
                    SoftwareInformation::class,
                    [
                        SoftwareInformation::NAME    => null,
                        SoftwareInformation::VENDOR  => null,
                        SoftwareInformation::VERSION => null,
                    ],
                    Asset::class,
                ]),
            ]),
            'configuration'  => collect([
                'persistPopulatedEntity' => collect([
                    OpenPort::class,
                    [
                        OpenPort::NUMBER    => null,
                        OpenPort::ASSET_ID  => null,
                    ],
                    Asset::class,
                ]),
            ]),
            'vulnerability' => collect([
                'addTemporaryRawDataToEntity' => collect([
                    Vulnerability::class,
                    'getIdFromScanner',
                    'genericOutput',
                    'setGenericOutput'
                ]),
                'persistPopulatedEntity' => collect([
                    Vulnerability::class,
                    [
                        Vulnerability::ID_FROM_SCANNER => null,
                        Vulnerability::NAME            => null,
                    ],
                    Asset::class,
                ]),
            ]),
            'exploit'       => collect([
                'addToParentForCascadePersist' => collect([
                    Exploit::class,
                    Vulnerability::class,
                    [
                        Exploit::TITLE       => null,
                        Exploit::SKILL_LEVEL => null,
                    ]
                ]),
            ]),
            'reference'     => collect([
                'persistPopulatedEntity' => collect([
                    VulnerabilityReferenceCode::class,
                    [
                        VulnerabilityReferenceCode::REFERENCE_TYPE,
                        VulnerabilityReferenceCode::VALUE,
                        VulnerabilityReferenceCode::VULNERABILITY,
                    ],
                    Vulnerability::class,
                    false,
                    false,
                ]),
            ]),
            'VulnerabilityDefinitions' => collect([
                'flushDoctrineUnitOfWork',
            ]),
        ]);

        $this->genericOutput = new Collection();
    }

    /**
     * @inheritdoc
     */
    protected function getBaseTagName()
    {
        return 'node';
    }

    /**
     * @inheritdoc
     */
    public function getLogFilename(): string
    {
        return 'nexpose-xml-parser.json.log';
    }

    /**
     * @inheritdoc
     *
     * @return string
     */
    public function getStoragePath(): string
    {
        return storage_path('scans/xml/nexpose');
    }
}