<?php

namespace App\Services\Parsers;

use App\Contracts\ParsesXmlFiles;
use App\Entities\Asset;
use App\Entities\OpenPort;
use App\Entities\Workspace;
use App\Models\NmapModel;
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

class NmapXmlParserService extends AbstractXmlParserService implements ParsesXmlFiles
{
    /** NMAP XML node names */
    const XML_NODE_NAME_CPE      = 'cpe';
    const XML_NODE_NAME_PORT     = 'port';
    const XML_NODE_NAME_OS       = 'os';

    /** NMAP XML node attribute names */
    const XML_ATTRIBUTE_ACCURACY = 'accuracy';
    const XML_ATTRIBUTE_PORTID   = 'portid';

    /** @var int */
    protected $currentPortNumber;

    /** @var Collection */
    protected $accuracies;

    /**
     * NmapXmlParserService constructor.
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
            'osclass'                => new Collection([
                'setVendor' => new Collection([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'vendor',
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => Asset::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => [
                        'filled',
                        'regex:' . Asset::getValidVendorsRegex(),
                    ]
                ]),
                'setOsVersion' => new Collection([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'osgen',
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => Asset::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled',
                ]),
            ]),
            'address'                => new Collection([
                'setIpAddressV4' => new Collection([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'addr',
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => Asset::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => FILTER_FLAG_IPV4,
                ]),
                'setIpAddressV6' => new Collection([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'addr',
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => Asset::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => FILTER_FLAG_IPV6,
                ]),
                'setMacAddress'  => new Collection([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'addr',
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => Asset::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => [
                        'filled',
                        'regex:' . Asset::REGEX_MAC_ADDRESS,
                    ]
                ]),
                'setMacVendor'   => new Collection([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'vendor',
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => Asset::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => new Collection([
                        parent::MAP_ATTRIBUTE_MAIN_VALIDATION    => ['vendor' => 'filled'],
                        parent::MAP_ATTRIBUTE_RELATED_VALIDATION => new Collection(['addrtype' => 'filled|in:mac']),
                    ]),
                ]),
            ]),
            'hostname'               => new Collection([
                'setHostname' => new Collection([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'name',
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => Asset::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => [
                        'filled',
                        'regex:' . Asset::REGEX_HOSTNAME
                    ]
                ]),
            ]),
            self::XML_NODE_NAME_CPE  => new Collection([
                'setCpe' => new Collection([
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS => Asset::class,
                    parent::MAP_ATTRIBUTE_VALIDATION   => [
                        'filled',
                        'regex:' . Asset::REGEX_CPE
                    ],
                ]),
            ]),
            self::XML_NODE_NAME_PORT => new Collection([
                'setNumber' => new Collection([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => self::XML_ATTRIBUTE_PORTID,
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => OpenPort::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled|int',
                ]),
                'setProtocol'   => new Collection([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'protocol',
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => OpenPort::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled',
                ]),
            ]),
            'service'                => new Collection([
                'setServiceName'        => new Collection([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'name',
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => OpenPort::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled',
                ]),
                'setServiceProduct'     => new Collection([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'product',
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => OpenPort::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled',
                ]),
                'setServiceExtraInfo'   => new Collection([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'extrainfo',
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => OpenPort::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled',
                ]),
                'setServiceFingerPrint' => new Collection([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'servicefp',
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => OpenPort::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled',
                ]),
            ]),
            'uptime'                 => new Collection([
                'setUptime'   => new Collection([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'seconds',
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => Asset::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled|int',
                ]),
                'setLastBoot' => new Collection([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'lastboot',
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => Asset::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled',
                ]),
            ]),
        ]);

        $this->nodePreprocessingMap = new Collection([
            'host'    => new Collection([
                'initialiseNewEntity' => collect([
                    Asset::class,
                ]),
            ]),
            'port'    => collect([
                'initialiseNewEntity' => collect([
                    OpenPort::class,
                ]),
            ]),
            'osmatch' => 'isMoreAccurate',
            'state'   => 'skipNonOpenPortAndMoveOnToNext',
        ]);

        $this->nodePostProcessingMap = collect([
            'hostnames' => collect([
                'persistPopulatedEntity' => collect([
                    Asset::class,
                    [
                        Asset::HOSTNAME      => null,
                        Asset::IP_ADDRESS_V4 => null,
                    ],
                    Workspace::class
                ]),
            ]),
            'port'      => collect([
                'persistPopulatedEntity' => collect([
                    OpenPort::class,
                    [
                        OpenPort::NUMBER   => null,
                        OpenPort::ASSET_ID => null,
                    ],
                    Asset::class,
                ]),
            ]),
            'nmaprun'   => 'flushDoctrineUnitOfWork',
        ]);

        $this->accuracies = new Collection();

        // Instantiate a model
        $this->model = new NmapModel();
    }

    /**
     * Get the value of the accuracy attribute for the current XML node. Returns null if the node doesn't exist
     *
     * @return string|null
     */
    protected function getXmlNodeAccuracyAttributeValue()
    {
        return $this->parser->getAttribute(self::XML_ATTRIBUTE_ACCURACY);
    }

    /**
     * Check if the current node's accuracy attribute is set
     *
     * @return bool
     */
    protected function nodeHasAccuracyAttribute(): bool
    {
        // Look for an accuracy attribute and validate it
        $accuracy = $this->getXmlNodeAccuracyAttributeValue();
        return isset($accuracy);
    }

    /**
     * @inheritdoc
     * Check if the accuracy of this node is greater than the accuracy for the current model value related to this node
     *
     * @param string $attribute
     * @return bool
     */
    protected function isMoreAccurate()
    {
        $vendorName = $this->parser->getAttribute('name');
        $validation = ['filled', 'regex:' . Asset::getValidVendorsRegex()];
        if (!$this->isValidXmlValueOrAttribute('name', $validation, $vendorName)) {
            $this->skipToNextNode(collect(['osmatch', 'uptime']));
            return false;
        }

        $accuracy = $this->getXmlNodeAccuracyAttributeValue();
        $accuracyIsValid = $accuracy !== true && parent::isValidXmlValueOrAttribute(
            self::XML_ATTRIBUTE_ACCURACY, 'filled|integer|between:0,100', $accuracy
        );

        // Get the name of the current node to use as an index for the accuracy value
        $nodeName = $this->parser->name;

        // If accuracy is invalid or less than the current accuracy for this attribute, return null so that nothing
        // nothing further is done on this node
        if (!$accuracyIsValid || $accuracy <= $this->model->getCurrentAccuracyFor($nodeName)) {
            $this->skipToNextNode(collect(['osmatch', 'uptime']));
            return false;
        }

        // Attempt to extract the CPE for osclass nodes that have the highest accuracy
        $this->extractCpe();

        // Set the accuracy to the value of the accuracy attribute for this node
        // and return true so that it is added to the model
        $this-> model->setCurrentAccuracyFor($nodeName, intval($accuracy));

        return true;
    }

    /**
     * Attempt to extract the CPE for the most accurate osmatch
     */
    protected function extractCpe()
    {
        // only check osclass nodes
        if (!$nodeName = $this->parser->name !== 'osclass') {
            return false;
        }

        // Expand the XML node into a DOMNode so that we can extract the text value
        $domNode = $this->parser->expand();
        /** @var \DOMNode $childNode */
        foreach ($domNode->childNodes as $childNode) {
            // Only attempt extract from nodes named 'cpe' that have a text value
            if (!$childNode->nodeName === self::XML_NODE_NAME_CPE || empty($childNode->nodeValue)) {
                continue;
            }

            $validationRules = $this->fileToSchemaMapping
                ->get(self::XML_NODE_NAME_CPE)
                ->get(self::MAP_ATTRIBUTE_VALIDATION);

            // Make sure we have a valid CPE
            if (!parent::isValidXmlValueOrAttribute(
                self::NODE_TEXT_VALUE_DEFAULT, $validationRules, $childNode->nodeValue
            )) {
                continue;
            }

            // Set the CPE on the model
            $this->entities->get(Asset::class)->setCpe($childNode->nodeValue);
        }

        return true;
    }

    /**
     * If the state of this port is not open skip it and move on to the next port or os node
     */
    protected function skipNonOpenPortAndMoveOnToNext()
    {
        if ($this->parser->getAttribute('state') === 'open') {
            return;
        }

        $this->skipToNextNode(collect([self::XML_NODE_NAME_PORT, self::XML_NODE_NAME_OS]));
    }

    /**
     * Keep skipping nodes until the current node name matches one of the names in the given Collection
     *
     * @param Collection $nodeNames
     */
    protected function skipToNextNode(Collection $nodeNames)
    {
        while (!$nodeNames->contains($this->parser->name)) {
            $this->parser->next();
        }
    }

    /**
     * @inheritdoc
     */
    protected function resetModel()
    {
        $this->model = new NmapModel();
    }

    /**
     * @return NmapModel
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return int
     */
    public function getCurrentPortNumber(): int
    {
        return $this->currentPortNumber;
    }

    /**
     * @param int $currentPortNumber
     */
    public function setCurrentPortNumber(int $currentPortNumber)
    {
        $this->currentPortNumber = $currentPortNumber;
    }

    /**
     * Get the current accuracy for the given field
     *
     * @param string $field
     * @return int
     */
    public function getCurrentAccuracyFor(string $field): int
    {
        return $this->accuracies->get($field, 0);
    }

    /**
     * Set the accuracy for the given field
     *
     * @param string $field
     * @param int $accuracy
     */
    public function setCurrentAccuracyFor(string $field, int $accuracy)
    {
        $this->accuracies->put($field, $accuracy);
    }

    /**
     * @inheritdoc
     */
    protected function getBaseTagName()
    {
        return 'host';
    }
    
    /**
     * @inheritdoc
     */
    public function getStoragePath(): string
    {
        return storage_path('scans/xml/nmap');
    }
}