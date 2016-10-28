<?php

namespace App\Services\Parsers;

use App\Entities\Asset;
use App\Entities\Vulnerability;
use App\Entities\Workspace;
use App\Repositories\AssetRepository;
use App\Repositories\FileRepository;
use App\Services\JsonLogService;
use Doctrine\ORM\EntityManager;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Validation\Factory;
use League\Tactician\CommandBus;
use XMLReader;

class NessusXmlParserService extends AbstractXmlParserService
{
    /** @var string */
    private $reportItemEntityClass;

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
            'HostProperties.tag' => collect([
                'setIpAddressV4' => collect([
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => Asset::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => collect([
                        parent::MAP_ATTRIBUTE_MAIN_VALIDATION => FILTER_FLAG_IPV4,
                        parent::MAP_ATTRIBUTE_RELATED_VALIDATION => collect([
                            'name' => 'filled|in:host-ip'
                        ]),
                    ]),
                ]),
                'setMacAddress' => collect([
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
                'setSeverity' => collect([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'severity',
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => Vulnerability::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled|int',
                ]),
                'setIdFromScanner' => collect([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'pluginID',
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => Vulnerability::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled',
                ]),
                'setName' => collect([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'pluginName',
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => Vulnerability::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled',
                ]),
                'setHttpPort' => collect([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'port',
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => Vulnerability::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled|int|min:1',
                ]),
            ]),
            'cvss_base_score' => collect([
                'setCvssScore' => collect([
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => Vulnerability::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => [
                        'filled',
                        'regex:/^\d*(\.\d{1,2})?$/',
                    ],
                ]),
            ]),
            'description' => collect([
                'setDescription' => collect([
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => Vulnerability::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled',
                ]),
            ]),
            'vuln_publication_date' => collect([
                'setPublishedDateFromScanner' => collect([
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => Vulnerability::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled',
                ]),
            ]),
            'solution' => collect([
                'setSolution' => collect([
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => Vulnerability::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled',
                ]),
            ]),
            'plugin_output' => collect([
                'setGenericOutput' => collect([
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => Vulnerability::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled',
                ]),
            ]),
            'plugin_type' => collect([
                'setGenericOutput' => collect([
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => Vulnerability::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled|in:local,remote',
                ]),
            ]),
        ]);

        // Pre-processing method map
        $this->nodePreprocessingMap = collect([
            'ReportHost'        => collect([
                'initialiseNewEntity' => collect([
                    Asset::class,
                ]),
            ]),
            'ReportItem' => 'getEntityClassAndInitialise',
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
            'ReportHost' => 'flushDoctrineUnitOfWork',
        ]);
    }

    /**
     * Override the parent method to store a persistent value for the entity class being used to process the current
     * report item
     *
     * @param string $entityClass
     */
    protected function initialiseNewEntity(string $entityClass)
    {
        $this->reportItemEntityClass = $entityClass;
        parent::initialiseNewEntity($entityClass);
    }

    /**
     * Try to get a valid entity class based on the Nessus plugin family
     * and then initialise a new instance of the entity
     */
    protected function getEntityClassAndInitialise()
    {
        $entityClass = $this->getEntityClassByPluginFamily();
        if (empty($entityClass) || !class_exists($entityClass)) {
            $this->parser->next();
            return;
        }

        $this->initialiseNewEntity($entityClass);
    }

    protected function getEntityClassAndPersist()
    {
        $this->persistPopulatedEntity(
            $this->reportItemEntityClass,
            $this->entities->get($this->reportItemEntityClass)->getUniqueKeyColumns()->all(),
            Asset::class
        );
    }

    /**
     * Get the relevant Entity class based on the Nessus plugin family
     *
     * @return string
     */
    protected function getEntityClassByPluginFamily(): string
    {
        $pluginFamily = $this->getXmlNodeAttributeValue('pluginFamily');
        return $this->getNessusPluginFamilies()->get($pluginFamily, false);
    }

    /**
     * Get the full collection of Nessus pluginFamily values mapped to an entity class
     *
     * @return Collection
     */
    protected function getNessusPluginFamilies(): Collection
    {
        return collect([
            'AIX Local Security Checks'          => Vulnerability::class,
            'Amazon Linux Local Security Checks' => Vulnerability::class,
            'Backdoors'                          => Vulnerability::class,
            'Brute Force Attacks'                => Vulnerability::class,
            'CentOS Local Security Checks'       => Vulnerability::class,
            'CGI abuses'                         => Vulnerability::class,
            'CGI abuses : XSS'                   => Vulnerability::class,
            'CISCO'                              => Vulnerability::class,
            'Databases'                          => Vulnerability::class,
            'Debian Local Security Checks'       => Vulnerability::class,
            'Default Unix Accounts'              => Vulnerability::class,
            'Denial of Service'                  => Vulnerability::class,
            'DNS'                                => Vulnerability::class,
            'F5 Networks Local Security Checks'  => Vulnerability::class,
            'Fedora Local Security Checks'       => Vulnerability::class,
            'Firewalls'                          => Vulnerability::class,
            'FreeBSD Local Security Checks'      => Vulnerability::class,
            'FTP'                                => Vulnerability::class,
            'Gain a shell remotely'              => Vulnerability::class,
            'General'                            => Vulnerability::class,
            'Gentoo Local Security Checks'       => Vulnerability::class,
            'HP-UX Local Security Checks'        => Vulnerability::class,
            'Huawei Local Security Checks'       => Vulnerability::class,
            'Incident Response'                  => Vulnerability::class,
            'Junos Local Security Checks'        => Vulnerability::class,
            'MacOS X Local Security'             => Vulnerability::class,
            'Mandriva Local Security'            => Vulnerability::class,
            'Misc.'                              => Vulnerability::class,
            'Mobile Devices'                     => Vulnerability::class,
            'Netware'                            => Vulnerability::class,
            'Oracle Linux Local Security'        => Vulnerability::class,
            'OracleVM Local Security'            => Vulnerability::class,
            'Palo Alto Local Security'           => Vulnerability::class,
            'Peer-To-Peer File Sharing'          => Vulnerability::class,
            'Policy Compliance'                  => Vulnerability::class,
            'Port Scanners'                      => false,//OpenPort::class,
            'Red Hat Local Security Checks'      => Vulnerability::class,
            'RPC'                                => Vulnerability::class,
            'SCADA'                              => Vulnerability::class,
            'Scientific Linux Local Security'    => Vulnerability::class,
            'Service detection'                  => false,//OpenPort::class,
            'Settings'                           => false,
            'Slackware Local Security'           => Vulnerability::class,
            'SMTP problems'                      => Vulnerability::class,
            'SNMP'                               => Vulnerability::class,
            'Solaris Local Security Checks'      => Vulnerability::class,
            'SuSE Local Security Checks'         => Vulnerability::class,
            'Ubuntu Local Security Checks'       => Vulnerability::class,
            'VMware ESX Local Security Checks'   => Vulnerability::class,
            'Web Servers'                        => Vulnerability::class,
            'Windows'                            => Vulnerability::class,
            'Windows : Microsoft Bulletins'      => Vulnerability::class,
            'Windows : User management'          => Vulnerability::class,
            'CGI'                                => Vulnerability::class,
            'Cloud Services'                     => Vulnerability::class,
            'Database'                           => Vulnerability::class,
            'Data Leakage'                       => Vulnerability::class,
            'DNS Servers'                        => Vulnerability::class,
            'Finger'                             => Vulnerability::class,
            'FTP Clients'                        => Vulnerability::class,
            'FTP Servers'                        => Vulnerability::class,
            'Generic'                            => Vulnerability::class,
            'IMAP Servers'                       => Vulnerability::class,
            'Internet Messengers'                => Vulnerability::class,
            'Internet Services'                  => Vulnerability::class,
            'IoT'                                => Vulnerability::class,
            'IRC Clients'                        => Vulnerability::class,
            'IRC Servers'                        => Vulnerability::class,
            'Malware'                            => Vulnerability::class,
            'Operating System Detection'         => false,
            'Policy'                             => Vulnerability::class,
            'POP Server'                         => Vulnerability::class,
            'Samba'                              => Vulnerability::class,
            'SMTP Clients'                       => Vulnerability::class,
            'SMTP Servers'                       => Vulnerability::class,
            'SSH'                                => Vulnerability::class,
            'Web Clients'                        => Vulnerability::class,
            'access-denied'                      => Vulnerability::class,
            'application'                        => Vulnerability::class,
            'connection'                         => Vulnerability::class,
            'continuous'                         => Vulnerability::class,
            'data-leak'                          => Vulnerability::class,
            'database'                           => Vulnerability::class,
            'detected-change'                    => Vulnerability::class,
            'dhcp'                               => Vulnerability::class,
            'dns'                                => Vulnerability::class,
            'information'                        => Vulnerability::class,
            'dos'                                => Vulnerability::class,
            'error'                              => Vulnerability::class,
            'file-access'                        => Vulnerability::class,
            'firewall'                           => Vulnerability::class,
            'honeypot'                           => Vulnerability::class,
            'Indicator'                          => Vulnerability::class,
            'intrusion'                          => Vulnerability::class,
            'lce'                                => Vulnerability::class,
            'login'                              => Vulnerability::class,
            'login-failure'                      => Vulnerability::class,
            'logout'                             => Vulnerability::class,
            'nbs'                                => Vulnerability::class,
            'network'                            => Vulnerability::class,
            'process'                            => Vulnerability::class,
            'restart'                            => Vulnerability::class,
            'social-networks'                    => Vulnerability::class,
            'scanning'                           => Vulnerability::class,
            'spam'                               => Vulnerability::class,
            'stats'                              => Vulnerability::class,
            'system'                             => Vulnerability::class,
            'threatlist'                         => Vulnerability::class,
            'usb'                                => Vulnerability::class,
            'virus'                              => Vulnerability::class,
            'vulnerability'                      => Vulnerability::class,
            'web-access'                         => Vulnerability::class,
            'web-error'                          => Vulnerability::class,
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