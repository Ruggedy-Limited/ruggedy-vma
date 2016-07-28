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
    const XML_ATTRIBUTE_ACCURACY = 'accuracy';
    const XML_NODE_NAME_CPE      = 'cpe';
    
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
                        'main'    => ['vendor' => 'filled'],
                        'related' => new Collection(['addrtype' => 'filled|in:mac']),
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
                parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => false,
                parent::MAP_ATTRIBUTE_VALIDATION    => [
                    'filled',
                    'regex:' . Asset::REGEX_CPE
                ],
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
        return $this->getParser()->getAttribute(self::XML_ATTRIBUTE_ACCURACY);
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
        $nodeName = $this->getParser()->name;

        // If accuracy is invalid or less than the current accuracy for this attribute, return null so that nothing
        // nothing further is done on this node
        if (!$accuracyIsValid || $accuracy <= $this->getModel()->getCurrentAccuracyFor($nodeName)) {
            return false;
        }

        // Attempt to extract the CPE for osclass nodes that have the highest accuracy
        $this->extractCpe();

        // Set the accuracy to the value of the accuracy attribute for this node
        // and return true so that it is added to the model
        $this->getModel()->setCurrentAccuracyFor($nodeName, intval($accuracy));

        return true;
    }

    /**
     * Attempt to extract the CPE for the most accurate osmatch
     */
    protected function extractCpe()
    {
        // only check osclass nodes
        if (!$nodeName = $this->getParser()->name !== 'osclass') {
            return false;
        }

        // Expand the XML node into a DOMNode so that we can extract the text value
        $domNode = $this->getParser()->expand();
        /** @var \DOMNode $childNode */
        foreach ($domNode->childNodes as $childNode) {
            // Only attempt extract from nodes named 'cpe' that have a text value
            if (!$childNode->nodeName === self::XML_NODE_NAME_CPE || empty($childNode->nodeValue)) {
                continue;
            }

            $validationRules = $this->getFileToSchemaMapping()
                ->get(self::XML_NODE_NAME_CPE)
                ->get(self::MAP_ATTRIBUTE_VALIDATION);

            // Make sure we have a valid CPE
            if (!parent::isValidXmlValueOrAttribute(
                self::NODE_TEXT_VALUE_DEFAULT, $validationRules, $childNode->nodeValue
            )) {
                continue;
            }

            // Set the CPE on the model
            $this->getModel()->setCpe($childNode->nodeValue);
        }
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