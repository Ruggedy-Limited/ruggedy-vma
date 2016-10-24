<?php

namespace App\Services\Parsers;

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
use Illuminate\Filesystem\Filesystem;
use Illuminate\Validation\Factory;
use League\Tactician\CommandBus;
use XMLReader;

class NessusXmlParserService extends AbstractXmlParserService
{
    /**
     * NessusXmlParserService constructor.
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
            'ReportHost' => collect([
                'setName' => collect([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'name',
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => Asset::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => "filled",
                ]),
            ]),
            'tag' => collect([
                'setIpAddressV4' => collect([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'name',
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => Asset::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => collect([
                        parent::MAP_ATTRIBUTE_MAIN_VALIDATION => FILTER_FLAG_IPV4,
                        parent::MAP_ATTRIBUTE_RELATED_VALIDATION => collect([
                            'name' => 'filled|in:host-ip'
                        ]),
                    ]),
                ]),
                'setMacAddress' => collect([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'name',
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => Asset::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => collect([
                        parent::MAP_ATTRIBUTE_MAIN_VALIDATION => [
                            'filled',
                            'regex:' . Asset::REGEX_MAC_ADDRESS,
                        ],
                        parent::MAP_ATTRIBUTE_RELATED_VALIDATION => collect([
                            'name' => 'filled|in:mac-address'
                        ]),
                    ]),
                ]),
                'setHostname' => collect([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'name',
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => Asset::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => collect([
                        parent::MAP_ATTRIBUTE_MAIN_VALIDATION => [
                            'filled',
                            'regex:' . Asset::REGEX_HOSTNAME
                        ],
                        parent::MAP_ATTRIBUTE_RELATED_VALIDATION => collect([
                            'name' => 'filled|in:host-fqdn'
                        ]),
                    ]),
                ]),
                'setNetbios' => collect([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'name',
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => Asset::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => collect([
                        parent::MAP_ATTRIBUTE_MAIN_VALIDATION => [
                            'filled',
                            'regex:' . Asset::REGEX_NETBIOS_NAME
                        ],
                        parent::MAP_ATTRIBUTE_RELATED_VALIDATION => collect([
                            'name' => 'filled|in:netbios-name'
                        ]),
                    ]),
                ]),
                'setCpe' => collect([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'name',
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => Asset::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => collect([
                        parent::MAP_ATTRIBUTE_MAIN_VALIDATION => [
                            'filled',
                            'regex:' . Asset::REGEX_CPE
                        ],
                        parent::MAP_ATTRIBUTE_RELATED_VALIDATION => collect([
                            'name' => 'filled|in:cpe-0'
                        ]),
                    ]),
                ]),
                'setVendor' => collect([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'name',
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => Asset::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => collect([
                        parent::MAP_ATTRIBUTE_MAIN_VALIDATION => [
                            'filled',
                            'regex:' . Asset::getValidVendorsRegex()
                        ],
                        parent::MAP_ATTRIBUTE_RELATED_VALIDATION => collect([
                            'name' => 'filled|in:operating-system'
                        ]),
                    ]),
                ]),
                'setOsVersion' => collect([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'name',
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => Asset::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => collect([
                        parent::MAP_ATTRIBUTE_MAIN_VALIDATION => [
                            'filled',
                        ],
                        parent::MAP_ATTRIBUTE_RELATED_VALIDATION => collect([
                            'name' => 'filled|in:operating-system'
                        ]),
                    ]),
                ]),
            ]),
            'ReportItem' => collect([
                parent::MAP_ATTRIBUTE_VALIDATION    => collect([
                    parent::MAP_ATTRIBUTE_MAIN_VALIDATION => [
                        'filled',
                    ],
                    parent::MAP_ATTRIBUTE_RELATED_VALIDATION => collect([
                        'pluginName' => 'filled|not_in:Patch Report,Nessus Scan Information'
                    ]),
                ]),
            ])
        ]);

        // Pre-processing method map
        $this->nodePreprocessingMap = collect([
            'ReportHost'        => collect([
                'initialiseNewEntity' => collect([
                    Asset::class,
                ]),
            ]),
            'ReportItem' => collect([
                'initialiseNewEntity' => collect([
                    Vulnerability::class,
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
            'HostProperties' => collect([
                'persistPopulatedEntity' => collect([
                    Asset::class,
                    [
                        Asset::HOSTNAME      => null,
                        Asset::IP_ADDRESS_V4 => null,
                    ],
                    Workspace::class,
                ]),
            ]),
            'ReportItem' => collect([
                'persistPopulatedEntity' => collect([
                    Vulnerability::class,
                    [
                        Vulnerability::ID_FROM_SCANNER => null,
                        Vulnerability::NAME            => null,
                    ],
                    Asset::class,
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
    }

    /**
     * @inheritdoc
     * @return string
     */
    protected function getBaseTagName()
    {
        return 'Report';
    }

    /**
     * @inheritdoc
     * @return string
     */
    public function getStoragePath(): string
    {
        return storage_path('scans/xml/nessus');
    }
}