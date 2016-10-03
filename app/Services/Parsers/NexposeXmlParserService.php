<?php

namespace App\Services\Parsers;

use App\Commands\CreateAsset;
use App\Contracts\GeneratesUniqueHash;
use App\Contracts\HasIdColumn;
use App\Contracts\ParsesXmlFiles;
use App\Contracts\RelatesToFiles;
use App\Contracts\SystemComponent;
use App\Entities\Asset;
use App\Entities\Base\AbstractEntity;
use App\Entities\File;
use App\Entities\OpenPort;
use App\Entities\SoftwareInformation;
use App\Entities\Vulnerability;
use App\Entities\VulnerabilityReferenceCode;
use App\Entities\Workspace;
use App\Models\NexposeModel;
use App\Models\SoftwareInformationModel;
use App\Repositories\AssetRepository;
use App\Repositories\FileRepository;
use App\Services\JsonLogService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\Factory;
use Illuminate\Filesystem\Filesystem;
use League\Tactician\CommandBus;
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

    /** @var CommandBus */
    protected $bus;

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
        $this->bus = App::make(CommandBus::class);

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
            'exploits'          => collect([
                'setExploitDescription' => collect([
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => Vulnerability::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => 'filled',
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
                    parent::MAP_ATTRIBUTE_VALIDATION    => [
                        'filled',
                        'regex:' . VulnerabilityReferenceCode::getValidReferenceCodeTypeRegex(),
                    ]
                ]),
                'setValue'      => collect([
                    parent::MAP_ATTRIBUTE_ENTITY_CLASS  => VulnerabilityReferenceCode::class,
                    parent::MAP_ATTRIBUTE_VALIDATION    => collect([
                        parent::MAP_ATTRIBUTE_MAIN_VALIDATION => 'filled',
                        parent::MAP_ATTRIBUTE_RELATED_VALIDATION => collect([
                            'source' => [
                                'filled',
                                'regex:' . VulnerabilityReferenceCode::getValidReferenceCodeTypeRegex(),
                            ]
                        ]),
                    ]),
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
                'initialiseNewEntity' => $vulnerabilityPreProcessing,
            ]),
            'vulnerability' => collect([
                'initialiseNewEntity' => $vulnerabilityPreProcessing,
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
            'test'        => collect([
                'moveToTemporaryCollection' => collect([
                    Vulnerability::class,
                    Asset::class,
                    'getIdFromScanner'
                ]),
            ]),
            'vulnerability' => collect([
                'mergeWithTemporaryCollectionAndPersist' => collect([
                    Vulnerability::class,
                    'getIdFromScanner',
                    [
                        Vulnerability::ID_FROM_SCANNER => null,
                        Vulnerability::NAME            => null,
                        Vulnerability::ASSET           => null,
                    ],
                    'getVulnerabilityReferenceCodes'
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
            // TODO: Move the above to the below key and change this to find all the VulnerabilityReferenceCodes in the
            // temporary Collection and persist them in preparation for flushing
            'VulnerabilityDefinitions' => collect([
                'flushDoctrineUnitOfWork',
            ]),
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
     * @param bool $isFromTemporary
     * @param bool $persist
     */
    protected function persistPopulatedEntity(
        string $entityClass, array $findOneByCriteria = [], string $parentEntityClass = '',
        bool $isFromTemporary = false, bool $persist = true
    )
    {
        // Get the relevant entity from the entities Collection and validate it.
        /** @var AbstractEntity $entity */
        $entity = $this->entities->get($entityClass);
        if (!$this->isValidEntity($entity, $entityClass) || $entity->hasMinimumRequiredPropertiesSet() !== true) {
            $this->logger->log(Logger::ERROR, "A valid entity object with the given class name was not found", [
                'entityClass' => $entityClass ?? null,
            ]);

            return;
        }

        // Persist Asset via the bus
        if ($entity instanceof Asset) {
            $command = new CreateAsset($this->file->getWorkspaceId(), $entity);
            $asset = $this->bus->handle($command);
            $this->entities->put(Asset::class, $asset);
            return;
        }

        $parentEntity = null;
        // Get the parent entity if one exists in the local Collection of entities
        if (!$isFromTemporary) {
            $parentEntity = $this->getParentEntityFromLocalCollection($parentEntityClass);
        }

        // Populate a criteria array with the values from the entity instance
        $findOneByCriteria = $this->getPopulatedCriteria($entity, $findOneByCriteria, $parentEntity);

        // Check if the entity is managed or exists in the DB
        $entity = $this->checkForExistingEntity($findOneByCriteria, $entityClass, $entity);

        // If this entity is being persisted from the temporary Collection, skip adding the File, User and Parent
        // relationships because this will already have been done, just persist and return
        if ($isFromTemporary) {
            $this->em->persist($entity);
            return;
        }

        // Add the file relation where relevant
        $this->addFileRelation($entity, $entityClass);

        // Add the User relation where the entity implements the SystemComponent contract
        $this->addUserRelation($entity);

        // Add the entity to the Entity Manager unless the persist
        if ($persist) {
            $this->em->persist($entity);
        }

        // Add this entity to the parent entity's Collection of related entities
        $this->setParentRelationship($entity, $entityClass, $parentEntityClass);
    }

    /**
     * Store an entity temporarily for more data to be added later in the scan
     *
     * @param string $entityClass
     * @param string $parentEntityClass
     * @param string $keyGetterMethod
     */
    protected function moveToTemporaryCollection(
        string $entityClass, string $parentEntityClass = '', string $keyGetterMethod = ''
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

        // Set this entity's parent where possible
        $this->setParentRelationship($entity, $entityClass, $parentEntityClass, true);

        // Add the file relation where relevant
        $this->addFileRelation($entity, $entityClass, true);

        // Add the User relation where the entity implements the SystemComponent contract
        $this->addUserRelation($entity);

        // Get the key to use as the offset to store the temporary entity at in the Collection
        $keyForCollection = $this->getKeyValueForTemporaryCollection($entity, $keyGetterMethod);
        if (empty($keyForCollection)) {
            return;
        }

        // If the offset does not yet exist, create it
        if (!$this->temporaryEntities->offsetExists($keyForCollection)) {
            $this->temporaryEntities->put($keyForCollection, new Collection());
        }

        if (!($entity instanceof GeneratesUniqueHash)) {
            // Push the entity onto the Collection
            $this->temporaryEntities->get($keyForCollection)->push($entity);
            return;
        }

        $this->temporaryEntities->get($keyForCollection)->put($entity->getHash(), $entity);
    }

    /**
     * Fetch a partially populated entity from the temporary entity Collection and add it's contents to the entity of
     * the same class in the $this->entities Collection and then persist the entity
     *
     * @param string $entityClass
     * @param string $keyGetterMethod
     * @param array $findOneBy
     * @param string $getChildrenMethod
     */
    protected function mergeWithTemporaryCollectionAndPersist(
        string $entityClass, string $keyGetterMethod, array $findOneBy = [], string $getChildrenMethod = ''
    )
    {
        if (!isset($keyGetterMethod, $entityClass) || !class_exists($entityClass)) {
            $this->logger->log(Logger::ERROR, "Invalid method parameter(s)", [
                'keyGetterMethod'      => "Expected string",
                'keyGetterMethodValue' => $keyGetterMethods ?? null,
                'entityClass'          => "Expected string",
                'entityClassValue'     => $entityClass ?? null,
            ]);

            return;
        }

        /** @var AbstractEntity $entity */
        $entity = $this->entities->get($entityClass);
        if (!$this->isValidEntity($entity, $entityClass)) {
            $this->logger->log(Logger::ERROR, "A valid entity object with the given class name was not found", [
                'entityClass' => $entityClass ?? null,
            ]);

            return;
        }

        $id = $this->getKeyValueForTemporaryCollection($entity, $keyGetterMethod);
        if (empty($id) || !$this->temporaryEntities->offsetExists($id)) {
            $this->logger->log(Logger::WARNING, "No temporary entity found at the relevant offset", [
                'entityClass'     => $entityClass,
                'offset'          => $id ?? null,
                'keyGetterMethod' => $keyGetterMethod,
            ]);

            return;
        }

        $entityCollection = $this->temporaryEntities->get($id);
        // If we got to this point we must have a Collection, otherwise we have something we can't work with, exit early
        if (!is_object($entityCollection) || !($entityCollection instanceof Collection)) {
            $this->logger->log(Logger::WARNING, "Unexpected scalar or object", [
                'entityClass'     => $entityClass,
                'offset'          => $id,
                'keyGetterMethod' => $keyGetterMethod,
                'typeEncountered' => is_object($entityCollection) ? gettype($entityCollection) : null,
            ]);

            return;
        }

        // Merge the current entity instance in $this->entities with each of the temporary entities with the same key
        $entityCollection->filter(function ($temporaryEntity) {
            // Defensiveness: make sure we only operate on instances of AbstractEntity
            return $temporaryEntity instanceof AbstractEntity;
        })->each(function ($temporaryEntity) use ($entity, $entityClass, $findOneBy, $getChildrenMethod) {
            // Merge the contents of the temporary entity with the current entity to form a single array
            /** @var AbstractEntity $temporaryEntity */
            //$entityContents = array_replace($entity->toArray(), $temporaryEntity->toArray(true));
            $temporaryEntity->setFromArray($entity->toArray(true));
            $this->refreshChildEntities($temporaryEntity, $getChildrenMethod);
            // Create a new instance of $entityClass in the $this->entities Collection and add the entity that contains
            // the merged contents of the current entity and the temporary entity
            //$this->initialiseNewEntity($entityClass);
            $this->entities->offsetUnset($entityClass);
            $this->entities->put($entityClass, $temporaryEntity);
            //$this->entities->get($entityClass)->setFromArray($entityContents);

            // Populate the new entity instance with the merged contents of the current and temporary entities
            // Persist the populated entity
            $this->persistPopulatedEntity($entityClass, $findOneBy, '', true);
            return true;
        });
    }

    /**
     * Because the children were added when the parent was not yet fully populated, and the parent object is recreated
     * from the temporary Collection, we need to refresh the parent on all the children so they are correctly related
     * to to complete parent at the correct memory reference
     *
     * @param AbstractEntity $parentEntity
     * @param string $getChildrenMethod
     */
    protected function refreshChildEntities(AbstractEntity $parentEntity, string $getChildrenMethod)
    {
        // Make sure we have a valid method to get the child entity collection
        if (empty($getChildrenMethod) || !method_exists($parentEntity, $getChildrenMethod)) {
            return;
        }

        // Make sure we get a valid collection of children
        $children = $parentEntity->$getChildrenMethod();
        if (empty($children) || !is_object($children) || !($children instanceof ArrayCollection)) {
            return;
        }

        // Refresh the parent for all the children
        $children->forAll(function($offset, $entity) use ($parentEntity) {
            $setter = $this->getRelationSetterMethod(get_class($entity), get_class($parentEntity));
            if (empty($setter)) {
                return true;
            }

            $this->setEntityRelation($entity, $parentEntity, $setter);
            return true;
        });
    }

    /**
     * Persist all the entities of a particular class that are found in the temporaryEntities Collection
     *
     * @param string $entityClass
     */
    protected function persistClassFromTemporaryCollection(string $entityClass)
    {
        if (!isset($entityClass) || !class_exists($entityClass)) {
            $this->logger->log(Logger::ERROR, "Invalid method parameter(s)", [
                'entityClass'          => "Expected string",
                'entityClassValue'     => $entityClass ?? null,
            ]);

            return;
        }

        $this->temporaryEntities->collapse()->filter(function ($entity) use ($entityClass) {
            return isset($entity) && is_object($entity) && $entity instanceof $entityClass;
        })->filter(function ($entity) {
            return empty($entity->getParent()->getId());
        })->each(function ($entity) {
            $this->em->persist($entity);
            return true;
        });
    }

    /**
     * Add the child entity to it's parent or if the $setParentOnChild switch is set to TRUE, set the parent on the
     * child entity, with defensiveness to exit where the correct conditions are not met for this to work.
     *
     * @param AbstractEntity $entity
     * @param string $entityClass
     * @param string $parentEntityClass
     * @param bool $setParentOnChild
     */
    protected function setParentRelationship(
        AbstractEntity $entity, string $entityClass, string $parentEntityClass, bool $setParentOnChild = false
    )
    {
        // Get the parent entity if one exists in the local Collection of entities
        $parentEntity = $this->getParentEntityFromLocalCollection($parentEntityClass);
        if (empty($parentEntity)) {
            $this->logger->log(Logger::DEBUG, "No parent entity found", [
                'entityClass'       => $entityClass,
                'parentEntityClass' => $parentEntityClass,
                'setParentOnChild'  => $setParentOnChild,
                'entityContents'    => $entity->toArray(true),
            ]);

            return;
        }

        if ($setParentOnChild && !empty($this->getRelationSetterMethod($entityClass, $parentEntityClass))) {
            $setterMethod = $this->getRelationSetterMethod($entityClass, $parentEntityClass);
            $this->setEntityRelation($entity, $parentEntity, $setterMethod);
            return;
        }

        $setterMethod = $this->getRelationSetterMethod($parentEntityClass, $entityClass);
        $this->setEntityRelation($parentEntity, $entity, $setterMethod);
    }

    /**
     * Get the setter method that will be used to either set the parent entity on the child entity, or to add the child
     * entity to the Collection on the parent entity
     *
     * @param string $primaryClass
     * @param string $secondaryClass
     * @return null|string
     */
    protected function getRelationSetterMethod(string $primaryClass, string $secondaryClass)
    {
        if (!isset($primaryClass, $secondaryClass)) {
            return null;
        }

        if (!$this->entityRelationshipSetterMap->offsetExists($primaryClass)) {
            return null;
        }

        return $this->entityRelationshipSetterMap->get($primaryClass)->get($secondaryClass);
    }

    /**
     * Set the relationship between entities
     *
     * @param AbstractEntity $entity
     * @param AbstractEntity $relatedEntity
     * @param string $setter
     */
    protected function setEntityRelation(AbstractEntity $entity, AbstractEntity $relatedEntity, string $setter)
    {
        if (empty($setter) || !method_exists($entity, $setter)) {
            return;
        }

        $entity->$setter($relatedEntity);
    }

    /**
     * Get the value from the entity that will be used as a key in the temporary Collection to be able to find the
     * entity later on
     *
     * @param AbstractEntity $entity
     * @param string $keyGetterMethod
     * @return null|string
     */
    protected function getKeyValueForTemporaryCollection(AbstractEntity $entity, string $keyGetterMethod)
    {
        if (empty($keyGetterMethod) || !method_exists($entity, $keyGetterMethod)) {
            return null;
        }

        return $entity->$keyGetterMethod();
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
     * @param $parentEntity
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
     * @param AbstractEntity $entity
     * @return object
     */
    protected function checkForExistingEntity(array $findOneByCriteria, string $entityClass, AbstractEntity $entity)
    {
        // Make sure we have both the relevant criteria to search and the entity class to generate a repository with
        if (empty($findOneByCriteria) || empty($entityClass) || !($entity instanceof HasIdColumn)) {
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
     * @param string $entityClass
     */
    protected function addFileRelation($entity, string $entityClass, $addFileToEntity = false)
    {
        if (empty($this->file) || !($this->file instanceof File)) {
            $this->logger->log(Logger::ERROR, "No file entity stored in service", [
                'description' => 'Trying to entity to the file being parsed as a relation and no file entity is present'
                    . ' in the service property.',
                'entityClass' => $entityClass ?? null,
            ]);

            return;
        }

        if (empty($entity) || !($entity instanceof RelatesToFiles)) {
            $this->logger->log(Logger::WARNING, "The given entity is not related to files", [
                'entityClass' => $entityClass ?? null,
            ]);

            return;
        }

        if ($addFileToEntity) {
            $entity->addFile($this->file);
            return;
        }

        $addToFileMethod = $this->getMethodNameToAddEntityFileRelation($entityClass);
        if (empty($addToFileMethod) || !method_exists($this->file, $addToFileMethod)) {
            $this->logger->log(Logger::ERROR, "Method to add file relation for this entity does not exist", [
                'addToFileMethod' => $addToFileMethod ?? null,
                'entityClass'     => $entityClass ?? null,
            ]);

            return;
        }

        $this->file->$addToFileMethod($entity);
    }

    /**
     * Set the User relation on the entity if the entity implements the SystemComponent contract
     *
     * @param AbstractEntity $entity
     */
    protected function addUserRelation(AbstractEntity $entity)
    {
        if (!($entity instanceof SystemComponent)) {
            return;
        }

        $entity->setUser(
            $this->file->getWorkspace()->getUser()
        );
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

        return !empty($entity) && $entity instanceof $entityClass;
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