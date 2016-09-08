<?php

namespace App\Services\Parsers;

use App\Contracts\ParsesXmlFiles;
use App\Entities\Asset;
use App\Models\NmapModel;
use App\Repositories\FileRepository;
use App\Services\JsonLogService;
use Doctrine\ORM\EntityManager;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Validation\Factory;
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
    
    /**
     * NmapXmlParserService constructor.
     *
     * @param XMLReader $parser
     * @param Filesystem $fileSystem
     * @param Factory $validatorFactory
     * @param FileRepository $fileRepository
     * @param EntityManager $em
     * @param JsonLogService $logger
     */
    public function __construct(
        XMLReader $parser, Filesystem $fileSystem, Factory $validatorFactory, FileRepository $fileRepository,
        EntityManager $em, JsonLogService $logger
    )
    {
        parent::__construct($parser, $fileSystem, $validatorFactory, $fileRepository, $em, $logger);

        // Create the mappings to use when parsing the NMAP XML output
        $this->fileToSchemaMapping = new Collection([
            'osclass'               => new Collection([
                'setOsVendor' => new Collection([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'vendor',
                    parent::MAP_ATTRIBUTE_VALIDATION    => [
                        'filled',
                        'regex:' . Asset::getValidVendorsRegex(),
                    ]
                ]),
            ]),
            'osmatch'               => new Collection([
                'setOsVersion' => new Collection([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'name',
                    parent::MAP_ATTRIBUTE_VALIDATION    => [
                        'filled',
                        'regex:' . Asset::REGEX_OS_VERSION,
                    ]
                ]),
            ]),
            'address'               => new Collection([
                'setIpV4'       => new Collection([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'addr',
                    parent::MAP_ATTRIBUTE_VALIDATION    => FILTER_FLAG_IPV4,
                ]),
                'setIpV6'       => new Collection([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'addr',
                    parent::MAP_ATTRIBUTE_VALIDATION    => FILTER_FLAG_IPV6,
                ]),
                'setMacAddress' => new Collection([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'addr',
                    parent::MAP_ATTRIBUTE_VALIDATION    => [
                        'filled',
                        'regex:' . Asset::REGEX_MAC_ADDRESS,
                    ]
                ]),
                'setMacVendor'  => new Collection([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'vendor',
                    parent::MAP_ATTRIBUTE_VALIDATION    => new Collection([
                        parent::MAP_ATTRIBUTE_MAIN_VALIDATION    => ['vendor' => 'filled'],
                        parent::MAP_ATTRIBUTE_RELATED_VALIDATION => new Collection(['addrtype' => 'filled|in:mac']),
                    ]),
                ]),
            ]),
            'hostname'              => new Collection([
                'setHostname' => new Collection([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'name',
                    parent::MAP_ATTRIBUTE_VALIDATION    => [
                        'filled',
                        'regex:' . Asset::REGEX_HOSTNAME
                    ]
                ]),
            ]),
            self::XML_NODE_NAME_CPE => new Collection([
                'setCpe' => new Collection([
                    parent::MAP_ATTRIBUTE_VALIDATION    => [
                        'filled',
                        'regex:' . Asset::REGEX_CPE
                    ],
                ]),
            ]),
            self::XML_NODE_NAME_PORT                  => new Collection([
                'setPortId'       => new Collection([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => self::XML_ATTRIBUTE_PORTID,
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled|int',
                ]),
                'setPortProtocol' => new Collection([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'protocol',
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled',
                ]),
            ]),
            'state'                 => new Collection([
                'setPortState' => new Collection([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'state',
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled|in:open',
                ])
            ]),
            'service'               => new Collection([
                'setPortServiceName' => new Collection([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'name',
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled',
                ]),
                'setPortServiceProduct' => new Collection([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'product',
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled',
                ]),
                'setPortExtraInfo' => new Collection([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'extrainfo',
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled',
                ]),
                'setPortFingerPrint' => new Collection([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'servicefp',
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled',
                ]),
            ]),
            'uptime'                => new Collection([
                'setUptime' => new Collection([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'seconds',
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled|int',
                ]),
                'setLastBoot' => new Collection([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'lastboot',
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled',
                ]),
            ]),
        ]);

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
        $accuracy = $this->getXmlNodeAccuracyAttributeValue();

        $accuracyIsValid = $accuracy !== true && parent::isValidXmlValueOrAttribute(
            self::XML_ATTRIBUTE_ACCURACY, 'filled|integer|between:0,100', $accuracy
        );

        // Get the name of the current node to use as an index for the accuracy value
        $nodeName = $this->parser->name;

        // If accuracy is invalid or less than the current accuracy for this attribute, return null so that nothing
        // nothing further is done on this node
        if (!$accuracyIsValid || $accuracy <= $this->model->getCurrentAccuracyFor($nodeName)) {
            return false;
        }

        // Attempt to extract the CPE for osclass nodes that have the highest accuracy
        $this->extractCpe();

        // Set the accuracy to the value of the accuracy attribute for this node
        // and return true so that it is added to the model
        $this->model->setCurrentAccuracyFor($nodeName, intval($accuracy));

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
            $this->model->setCpe($childNode->nodeValue);
        }

        return true;
    }

    /**
     * @inheritdoc
     * Override the base class method to set the current port number
     *
     * @param string $attribute
     * @return string
     */
    protected function getXmlNodeAttributeValue(string $attribute)
    {
        $value = parent::getXmlNodeAttributeValue($attribute);
        if ($attribute == self::XML_ATTRIBUTE_PORTID) {
            $value = intval($value);
            $this->currentPortNumber = $value;
        }

        return $value;
    }

    /**
     * @inheritdoc
     * Override the parent method to pass the port ID for the methods requiring it
     *
     * @param mixed $attributeValue
     * @param string $setter
     */
    protected function setValueOnModel($attributeValue, string $setter)
    {
        if ($this->model->getMethodsRequiringAPortId()->contains($setter)) {
            $this->model->$setter($this->currentPortNumber, $attributeValue);
            return;
        }

        parent::setValueOnModel($attributeValue, $setter);
    }

    /**
     * @inheritdoc
     * Override the parent method to add a check for the accuracy attribute
     *
     * @param string $attribute
     * @param $validationRules
     * @param mixed $value
     * @return bool
     */
    protected function isValidXmlValueOrAttribute(string $attribute, $validationRules, $value): bool
    {
        // Check that the parent class's validation method passes
        $isValid = parent::isValidXmlValueOrAttribute($attribute, $validationRules, $value);
        if (empty($isValid) && $attribute === 'state') {
            $this->removePortAndMoveOnToNext();
        }

        if (empty($isValid)) {
            return false;
        }

        // Check for an accuracy attribute and if there isn't one return true because the validation passed above
        if (!$this->nodeHasAccuracyAttribute()) {
            return true;
        }

        // The node attribute or text value only passes validation if this node's accuracy is higher than the accuracy
        // of the node where the model's current value was found
        return $this->isMoreAccurate();
    }

    /**
     * Remove the port from the list of ports if the state is not open and move on to the next port or os node
     */
    protected function removePortAndMoveOnToNext()
    {
        $this->model->removePort($this->currentPortNumber);
        while ($this->parser->name !== self::XML_NODE_NAME_PORT && $this->parser->name !== self::XML_NODE_NAME_OS) {
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