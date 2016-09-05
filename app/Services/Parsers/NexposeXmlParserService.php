<?php

namespace App\Services\Parsers;

use App\Contracts\ParsesXmlFiles;
use App\Entities\Asset;
use App\Models\NexposeModel;
use App\Models\SoftwareInformationModel;
use Doctrine\ORM\EntityManager;
use Illuminate\Support\Collection;
use Illuminate\Validation\Factory;
use App\Repositories\FileRepository;
use Illuminate\Filesystem\Filesystem;
use App\Services\JsonLogService;
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

    /** @var int */
    protected $currentPortNumber;

    /** @var SoftwareInformationModel */
    protected $softwareInformationModel;

    /**
     * NexposeXmlParserService constructor.
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
            'node' => new Collection([
                'setIpV4' => new Collection([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'address',
                    parent::MAP_ATTRIBUTE_VALIDATION    => FILTER_FLAG_IPV4,
                ]),
                'setMacAddress' => new Collection([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'address',
                    parent::MAP_ATTRIBUTE_VALIDATION    => [
                        'filled',
                        'regex:[A-Z0-9]{12}'
                    ],
                ]),
            ]),
            'os' => new Collection([
                'setOsVendor' => new Collection([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'family',
                    parent::MAP_ATTRIBUTE_VALIDATION    => new Collection([
                        parent::MAP_ATTRIBUTE_MAIN_VALIDATION => [
                            'filled',
                            'regex:' . Asset::REGEX_OS_VERSION,
                        ],
                        parent::MAP_ATTRIBUTE_RELATED_VALIDATION => new Collection([
                            self::XML_ATTRIBUTE_CERTAINTY => 'filled|in:1.00'
                        ]),
                    ]),
                ]),
                'setOsVersion' => new Collection([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'version',
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled'
                ]),
                'setOsProduct' => new Collection([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'product',
                    parent::MAP_ATTRIBUTE_VALIDATION    => new Collection([
                        parent::MAP_ATTRIBUTE_MAIN_VALIDATION => 'filled',
                        parent::MAP_ATTRIBUTE_RELATED_VALIDATION => new Collection([
                            self::XML_ATTRIBUTE_VERSION => 'filled',
                        ]),
                    ]),
                ]),
            ]),
            'fingerprint' => new Collection([
                'setSoftwareName' => new Collection([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'name',
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled'
                ]),
                'setSoftwareVersion'  => new Collection([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'version',
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled'
                ]),
                'setSoftwareVendor'  => new Collection([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'vendor',
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled'
                ]),
            ]),
        ]);

        $this->nodePreprocessingMap = new Collection([
            'fingerprint' => 'resetSoftwareInformationModel',
        ]);

        $this->nodePostProcessingMap = new Collection([
            'fingerprint' => 'addSoftwareInformationModelToCollection',
        ]);

        $this->model = new NexposeModel();
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
        if ($attribute == self::SOFTWARE_FINGERPRINTS) {
            return $this->getSoftwareInformationFingerprint();
        }

        $value = parent::getXmlNodeAttributeValue($attribute);
        if ($attribute == self::XML_ATTRIBUTE_PORT) {
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
        if ($this->getModel()->getMethodsRequiringAPortId()->contains($setter)) {
            $this->getModel()->$setter($this->getCurrentPortNumber(), $attributeValue);
            return;
        }

        parent::setValueOnModel($attributeValue, $setter);
    }

    /**
     * Get a Collection of software information from the <software><fingerprint></fingerprint></software> nodes
     *
     * @return Collection
     */
    protected function getSoftwareInformationFingerprint(): Collection
    {
        // At this point the parser should be on the <software></software> node and we need to get into the
        // and we need to get into the <fingerprint></fingerprint> nodes
        $this->getParser()->read();

        $softwareInformation = new Collection();

        while ($this->getParser()->name === self::XML_NODE_FINGERPRINT) {
            $model = new SoftwareInformationModel();

            // Make sure we have absolute certainty about the software before adding it
            $certainty = parent::getXmlNodeAttributeValue(self::XML_ATTRIBUTE_CERTAINTY);
            if ($certainty != 1) {
                $this->getParser()->read();
                continue;
            }

            // Get the product name, version and vendor from the current node's attributes
            $productName    = parent::getXmlNodeAttributeValue(self::XML_ATTRIBUTE_PRODUCT);
            $productVersion = parent::getXmlNodeAttributeValue(self::XML_ATTRIBUTE_VERSION);
            $productVendor  = parent::getXmlNodeAttributeValue(self::XML_ATTRIBUTE_VENDOR);

            // Set the product name, version and vendor on the SoftwareInformationModel
            $model->setName($productName);
            $model->setVersion($productVersion);
            $model->setVendor($productVendor);

            // Generate a unique hash for this software version and add the model to the Collection
            // using the hash as an index, if a model does not already exist at that index
            $hash = $model->getHash();
            if (!empty($softwareInformation->get($hash))) {
                continue;
            }

            $softwareInformation->put($hash, $model);
            $this->getParser()->read();
        }

        return $softwareInformation;
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
     * Reset the SoftwareInformation model to capture the information from a new node
     */
    protected function resetSoftwareInformationModel()
    {
        $this->getModel()->setTempSoftwareInformation(new SoftwareInformationModel());
    }

    /**
     * Add the SoftwareInformation model to the Collection of SoftwareInformation models
     */
    protected function addSoftwareInformationModelToCollection()
    {
        if (empty($this->getModel()->getTempSoftwareInformation())
            || !($this->getModel()->getTempSoftwareInformation() instanceof SoftwareInformationModel)) {
            return;
        }

        $hash = $this->getModel()->getTempSoftwareInformation()->getHash();
        if (empty($hash)) {
            return;
        }

        $this->getModel()->addSoftwareInformationFromTemp();
    }

    /**
     * @return NexposeModel
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @inheritdoc
     */
    protected function resetModel()
    {
        $this->model = new NexposeModel();
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
     *
     * @return string
     */
    public function getStoragePath(): string
    {
        return storage_path('scans/xml/nexpose');
    }
}