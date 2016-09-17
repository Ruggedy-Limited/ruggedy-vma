<?php

namespace App\Services\Parsers;

use App\Contracts\HasIdColumn;
use App\Contracts\ParsesXmlFiles;
use App\Contracts\RelatesToFiles;
use App\Entities\Asset;
use App\Entities\Base\AbstractEntity;
use App\Entities\File;
use App\Entities\OpenPort;
use App\Entities\SoftwareInformation;
use App\Entities\Vulnerability;
use App\Models\NexposeModel;
use App\Models\SoftwareInformationModel;
use App\Repositories\AssetRepository;
use App\Repositories\FileRepository;
use App\Services\JsonLogService;
use Doctrine\ORM\EntityManager;
use Illuminate\Support\Collection;
use Illuminate\Validation\Factory;
use Illuminate\Filesystem\Filesystem;
use Monolog\Logger;
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
     * @param AssetRepository $assetRepository
     * @param FileRepository $fileRepository
     * @param EntityManager $em
     * @param JsonLogService $logger
     */
    public function __construct(
        XMLReader $parser, Filesystem $fileSystem, Factory $validatorFactory, AssetRepository $assetRepository,
        FileRepository $fileRepository, EntityManager $em, JsonLogService $logger
    )
    {
        parent::__construct($parser, $fileSystem, $validatorFactory, $assetRepository, $fileRepository, $em, $logger);

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
            'test' => collect([
                'setIdFromScanner' => collect([
                    parent::MAP_ATTRIBUTE_XML_ATTRIBUTE => 'id',
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => Vulnerability::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled'
                ]),
                'setGenericOutput' => collect([
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => Vulnerability::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled'
                ])
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
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled'
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
                             ]
                        ]),
                    ]),
                ])
            ]),
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
                'initialiseNewEntity' => collect([
                    Vulnerability::class,
                ]),
            ]),
        ]);

        // Post-processing method map
        $this->nodePostProcessingMap = collect([
            'node.fingerprints' => collect([
                'persistPopulatedEntity' => collect([
                    Asset::class,
                    [
                        Asset::HOSTNAME      => null,
                        Asset::IP_ADDRESS_V4 => null,
                    ],
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
            'test'        => collect([
                'persistPopulatedEntity' => collect([
                    Vulnerability::class,
                    [
                        Vulnerability::NAME            => null,
                        Vulnerability::ID_FROM_SCANNER => null,
                        Vulnerability::ASSET_ID        => null,
                    ],
                    Asset::class,
                ]),
            ])
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
    protected function setValueOnModel($attributeValue, string $setter, string $entityClass = '')
    {
        // Check that we have an entity class. Without this we can't continue.
        if (empty($entityClass)) {
            $this->logger->log(Logger::ERROR, "Required at least a model class to set a value", [
                'attributeValue' => $attributeValue ?? null,
                'setter'         => $setter ?? null,
                'entityClass'    => $entityClass ?? null,
            ]);

            return false;
        }

        // Get the relevant entity from the entities Collection and validate it.
        $entity = $this->entities->get($entityClass);
        if (!$this->isValidEntity($entity, $entityClass)) {
            $this->logger->log(Logger::ERROR, "A valid entity object with the given class name was not found", [
                'attributeValue' => $attributeValue ?? null,
                'setter'         => $setter ?? null,
                'entityClass'    => $entityClass,
            ]);
            return false;
        }

        // Check that the setter method exists on the entity instance. If not, we can't continue.
        if (!method_exists($entity, $setter)) {
            $this->logger->log(Logger::ERROR, "Mapped setter does not exist on entity instance", [
                'attributeValue'   => $attributeValue ?? null,
                'setter'           => $setter ?? null,
                'entityClass'      => $entityClass,
            ]);

            return false;
        }

        // Call the setter method on the entity instance and pass in the value retrieved from the parser
        $entity->$setter($attributeValue);

        return true;
    }

    /**
     * Create a new instance of the relevant entity in the $entities Collection
     *
     * @param string $entityClass
     */
    protected function initialiseNewEntity(string $entityClass)
    {
        $this->entities->offsetUnset($entityClass);
        $this->entities->put($entityClass, new $entityClass());
    }

    /**
     * Persist an entity once we have iterated over the relevant fields in the current section of the XML and populated
     * it with the relevant data
     *
     * @param string $entityClass
     * @param array $findOneByCriteria
     * @param string $parentEntityClass
     */
    protected function persistPopulatedEntity(
        string $entityClass, array $findOneByCriteria = [], string $parentEntityClass = ''
    )
    {
        // Get the relevant entity from the entities Collection and validate it.
        $entity = $this->entities->get($entityClass);
        if (!$this->isValidEntity($entity, $entityClass)) {
            $this->logger->log(Logger::ERROR, "A valid entity object with the given class name was not found", [
                'entityClass' => $entityClass ?? null,
            ]);

            return;
        }

        // Get the parent entity if one exists in the local Collection of entities
        $parentEntity = $this->getParentEntityFromLocalCollection($parentEntityClass);

        // Populate a criteria array with the values from the entity instance
        $findOneByCriteria = $this->getPopulatedCriteria($entity, $findOneByCriteria, $parentEntity);

        // Check if the entity is managed or exists in the DB
        $entity = $this->checkForExistingEntity($findOneByCriteria, $entityClass, $entity);

        $this->addFileRelation($entity, $entityClass);

        // Add the entity to the Entity Manager
        $this->em->persist($entity);

        if (empty($parentEntity) || !$this->entityRelationshipSetterMap->offsetExists($parentEntityClass)) {
            return;
        }

        // Try and get the relevant setter method to use on the parent entity and exit early if it isn't found
        $setterMethod = $this->entityRelationshipSetterMap->get($parentEntityClass)->get($entityClass);
        if (empty($setterMethod) || !method_exists($parentEntity, $setterMethod)) {
            return;
        }

        // Add this entity to the relevant parent entity
        $parentEntity->$setterMethod($entity);
    }

    /**
     * Get the current Entity's parent from the local Collection of entities
     *
     * @param string $parentEntityClass
     * @return mixed|null
     */
    protected function getParentEntityFromLocalCollection(string $parentEntityClass)
    {
        // If there's no parent entity class given, there's nothing more to do, exit early
        if (empty($parentEntityClass)
            || !$this->isValidEntity($this->entities->get($parentEntityClass), $parentEntityClass)) {
            return null;
        }

        // Try and get the parent entity from the entities Collection and exit early if it isn't found
        return $this->entities->get($parentEntityClass);
    }

    /**
     * Populate the findOneByCriteria with the relevant values from the entity
     *
     * @param AbstractEntity $entity
     * @param array $criteria
     * @return array
     */
    protected function getPopulatedCriteria(AbstractEntity $entity, array $criteria, $parentEntity)
    {
        $populatedCriteria = array_intersect_key($entity->toArray(), $criteria);
        return $this->populateParentIdWhereRelevant($parentEntity, $populatedCriteria);
    }

    /**
     * @param $parentEntity
     * @param array $criteria
     * @return array
     */
    protected function populateParentIdWhereRelevant($parentEntity, array $criteria)
    {
        $parentId = $this->getParentIdFromParentEntity($parentEntity);
        if ($parentId === null) {
            return $criteria;
        }

        $idColumnName = $parentEntity::TABLE_NAME . '_id';
        if (!array_key_exists($idColumnName, $criteria)) {
            return $criteria;
        }

        $criteria[$idColumnName] = $parentId;

        return $criteria;
    }

    /**
     * @param $parentEntity
     * @return null|int
     */
    protected function getParentIdFromParentEntity($parentEntity)
    {
        if (empty($parentEntity) || !($parentEntity instanceof HasIdColumn)) {
            return null;
        }

        return $parentEntity->getId();
    }

    /**
     * Check for an existing entity in the entity hash value to ID value map Collection
     * @param array $findOneByCriteria
     * @param string $entityClass
     * @param HasIdColumn $entity
     * @return object
     */
    protected function checkForExistingEntity(array $findOneByCriteria, string $entityClass, HasIdColumn $entity)
    {
        // Make sure we have both the relevant criteria to search and the entity class to generate a repository with
        if (empty($findOneByCriteria) || empty($entityClass)) {
            return $entity;
        }

        // If the entity is already managed in the Entity Manager, return it
        if ($this->em->contains($entity)) {
            return $entity;
        }

        // Search for the entity in the database and if it exists, merge the given instance with the persistent instance
        $existingEntity = $this->em->getRepository($entityClass)->findOneBy($findOneByCriteria);
        if (!empty($existingEntity)) {
            $entity->setId($existingEntity->getId());
            $entity = $this->em->merge($entity);
        }

        return $entity;
    }

    /**
     * Add a file relation to persist to the many-to-many join table
     *
     * @param object $entity
     */
    protected function addFileRelation($entity, string $entityClass)
    {
        if (empty($this->file) || !($this->file instanceof File)) {
            return;
        }

        if (empty($entity) || !($entity instanceof RelatesToFiles)) {
            return;
        }

        $addToFileMethod = $this->getMethodNameToAddEntityFileRelation($entityClass);
        if (empty($addToFileMethod) || !method_exists($this->file, $addToFileMethod)) {
            return;
        }

        $this->file->$addToFileMethod($entity);
    }

    /**
     * Get the method name called on the file entity to add the relationship between this entity and the file
     *
     * @param string $entityClass
     * @return string
     */
    protected function getMethodNameToAddEntityFileRelation(string $entityClass)
    {
        $shortName = $this->getEntityShortClassName($entityClass);
        if (empty($shortName)) {
            return null;
        }

        return 'add' . $shortName;
    }

    /**
     * Check if the given entity is valid
     *
     * @param $entity
     * @param string $entityClass
     * @return bool
     */
    protected function isValidEntity($entity, string $entityClass = '')
    {
        if (empty($entityClass)) {
            return !empty($entity) && $entity instanceof AbstractEntity;
        }

        return !empty($entity) && $entity instanceof $entityClass && $entity instanceof AbstractEntity;
    }

    /**
     * Method for toggling mappings when the same node name is used for multiple sets of data
     *
     * @param $nodeName
     * @param Collection $mappings
     */
    protected function toggleMappings($nodeName, Collection $mappings)
    {
        // If this is the end element, unset the name mappings
        if ($this->parser->nodeType === XMLReader::END_ELEMENT) {
            $this->fileToSchemaMapping->offsetUnset($nodeName);
            return;
        }

        // If this is not the opening element, exit early and do nothing
        if ($this->parser->nodeType !== XMLReader::ELEMENT) {
            return;
        }

        // Add the name node mappings to the fileToSchemaMappings
        $this->fileToSchemaMapping->put('name', $mappings);
    }

    /**
     * Extract and concatenate all the attribute values from the current node and set them on the relevant model
     *
     * @param string $setter
     * @param string $entityClass
     * @param bool $includeAttributeNames
     * @param string $separator
     */
    protected function extractAndConcatenateAllAttributes(
        string $setter, string $entityClass, $includeAttributeNames = false, $separator = ','
    )
    {
        // Make sure the node has attributes
        if (!$this->parser->hasAttributes) {
            $this->logger->log(
                Logger::WARNING,
                "Function to extract XML attributes called on node that has no attributes",
                [
                    'nodeName'               => $this->parser->name ?? null,
                    'setter'                 => $setter ?? null,
                    'entityClass'            => $entityClass ?? null,
                    'includeAttributesNames' => $includeAttributeNames,
                    'separator'              => $separator,
                ]
            );
            return;
        }

        // Iterate over all the attributes and concatenate into a string
        $attributeValues = '';
        while ($this->parser->moveToNextAttribute()) {
            $currentAttribute = $this->parser->value;
            if ($includeAttributeNames) {
                $currentAttribute = $this->parser->name . "=" . $this->parser->value;
            }

            $attributeValues .= $currentAttribute . $separator;
        }

        // Trim the separator off the end of the string and add to the model
        $attributeValues = rtrim($attributeValues, $separator);
        $this->setValueOnModel($attributeValues, $setter, $entityClass);
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
        $this->model->setTempSoftwareInformation(new SoftwareInformationModel());
    }

    /**
     * Add the SoftwareInformation model to the Collection of SoftwareInformation models
     */
    protected function addSoftwareInformationModelToCollection()
    {
        if (empty($this->model->getTempSoftwareInformation())
            || !($this->model->getTempSoftwareInformation() instanceof SoftwareInformationModel)) {
            return;
        }

        $hash = $this->model->getTempSoftwareInformation()->getHash();
        if (empty($hash)) {
            return;
        }

        $this->model->addSoftwareInformationFromTemp();
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