<?php

namespace App\Services\Parsers;

use App\Commands\CreateAsset;
use App\Contracts\CustomLogging;
use App\Contracts\GeneratesUniqueHash;
use App\Contracts\HasIdColumn;
use App\Contracts\ParsesXmlFiles;
use App\Contracts\RelatesToFiles;
use App\Contracts\SystemComponent;
use App\Entities\Asset;
use App\Entities\Audit;
use App\Entities\Base\AbstractEntity;
use App\Entities\Exploit;
use App\Entities\File;
use App\Entities\OpenPort;
use App\Entities\SoftwareInformation;
use App\Entities\User;
use App\Entities\Vulnerability;
use App\Entities\VulnerabilityReferenceCode;
use App\Entities\Workspace;
use App\Exceptions\ParserMappingException;
use App\Repositories\AssetRepository;
use App\Repositories\FileRepository;
use App\Services\JsonLogService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Auth;
use Illuminate\Validation\Factory;
use League\Tactician\CommandBus;
use Monolog\Logger;
use ReflectionClass;
use XMLReader;

abstract class AbstractXmlParserService implements ParsesXmlFiles, CustomLogging
{
    /** Map attributes */
    const MAP_ATTRIBUTE_XML_ATTRIBUTE          = 'xmlAttribute';
    const MAP_ATTRIBUTE_VALIDATION             = 'validation';
    const MAP_ATTRIBUTE_MAIN_VALIDATION        = 'main';
    const MAP_ATTRIBUTE_RELATED_VALIDATION     = 'related';
    const MAP_ATTRIBUTE_ENTITY_CLASS           = 'entityClass';
    const MAP_ATTRIBUTE_CURRENT_KEY_NAME       = 'currentKeyName';
    const MAP_ATTRIBUTE_HOOK_METHOD            = 'hookMethod';
    const MAP_ATTRIBUTE_HOOK_METHOD_PARAMETERS = 'hookMethodParameters';
    const MAP_ATTRIBUTE_VERIFY_PARENT_NODE     = 'verifyParentNode';

    /** Attribute fallback value for validating & extracting the inner text value of a node */
    const NODE_TEXT_VALUE_DEFAULT = 'nodeTextValue';

    /** Match any XML tag */
    const REGEX_ANY_XML_TAG = '%((<.+(/)?>)|((<.+>)(.*)?(</.+>)?))%mi';

    /** The prefix for variables storing temporary entities */
    const TEMP_VARIABLE_PREFIX = 'temp';

    /** @var XMLReader */
    protected $parser;

    /** @var string */
    protected $previousNodeName;

    /** @var int */
    protected $previousNodeDepth;

    /** @var Collection */
    protected $parentNodeNames;

    /** @var Filesystem */
    protected $fileSystem;

    /** @var Factory */
    protected $validatorFactory;

    /** @var AssetRepository */
    protected $assetRepository;

    /** @var FileRepository */
    protected $fileRepository;

    /** @var EntityManager */
    protected $em;

    /** @var JsonLogService */
    protected $logger;

    /** @var CommandBus */
    protected $bus;
    
    /** @var Collection */
    protected $fileToSchemaMapping;

    /** @var Collection */
    protected $nodePreprocessingMap;

    /** @var Collection */
    protected $nodePostProcessingMap;

    /** @var Collection */
    protected $attributePreProcessingMap;

    /** @var Collection */
    protected $attributePostProcessingMap;

    /** @var Collection */
    protected $entityRelationshipSetterMap;

    /** @var Collection */
    protected $entities;

    /** @var Collection */
    protected $temporaryEntities;

    /** @var File */
    protected $file;

    /**
     * AbstractXmlParserService constructor.
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
        $this->parser           = $parser;
        $this->fileSystem       = $fileSystem;
        $this->validatorFactory = $validatorFactory;
        $this->fileRepository   = $fileRepository;
        $this->assetRepository  = $assetRepository;
        $this->em               = $em;

        $this->setLoggerContext($logger);
        $this->logger = $logger;
        $this->bus    = $commandBus;

        // Initialise Collection properties
        $this->parentNodeNames            = new Collection();
        $this->fileToSchemaMapping        = new Collection();
        $this->nodePreprocessingMap       = new Collection();
        $this->nodePostProcessingMap      = new Collection();
        $this->attributePreProcessingMap  = new Collection();
        $this->attributePostProcessingMap = new Collection();
        $this->entities                   = new Collection();
        $this->temporaryEntities          = new Collection();

        // A map of entity class and the various methods that add/set other related entities, indexed by the related
        // entity's class name
        $this->entityRelationshipSetterMap = new Collection([

            Asset::class => new Collection([
                Vulnerability::class       => 'addVulnerability',
                OpenPort::class            => 'addOpenPort',
                SoftwareInformation::class => 'addSoftwareInformation',
                Audit::class               => 'addAudit',
                File::class                => 'setFile',
            ]),

            Exploit::class => new Collection([
                Vulnerability::class => 'addVulnerability',
            ]),

            Vulnerability::class => new Collection([
                Asset::class                      => 'addAsset',
                VulnerabilityReferenceCode::class => 'addVulnerabilityReferenceCode',
                Exploit::class                    => 'addExploit',
            ]),

            VulnerabilityReferenceCode::class => new Collection([
                Vulnerability::class => 'setVulnerability',
            ]),

            OpenPort::class => new Collection([
                Asset::class => 'setAsset',
            ]),

            SoftwareInformation::class => new Collection([
                Asset::class => 'addAsset',
            ]),

            Audit::class => new Collection([
                Asset::class => 'addAsset',
            ]),
        ]);
    }

    /**
     * Parse an XML file and return the relevant model or false on failure
     *
     * @param File $file
     * @throws Exception
     * @throws FileNotFoundException
     */
    public function processXmlFile(File $file)
    {
        if (!isset($file)) {
            return;
        }

        // Check that the file exists
        if (!$this->fileSystem->exists($file->getPath())) {
            throw new FileNotFoundException("The given file was not found in the default location");
        }

        $this->file = $file;
        $this->entities->put(Workspace::class, $file->getWorkspaceApp()->getWorkspace());

        // Attempt to parse the XML and catch any exceptions
        try {
            $this->parser->open($file->getPath());

            // Authenticate the User who uploaded the file so that their permissions will be used for applying any
            // operations that result from processing the file
            $this->getAndAuthenticateFileUser($file);

            $this->parseXml();
        } catch (Exception $e) {
            $this->logger->log(Logger::ERROR, "Failed to parse XML file.", [
                'file'      => $file->getPath(),
                'user'      => $file->getUserId(),
                'workspace' => $file->getWorkspaceApp()->getWorkspaceId(),
                'exception' => $e->getMessage(),
                'trace'     => $this->logger->getTraceAsArrayOfLines($e),
            ]);

            throw $e;
        }

        $this->moveFileToProcessed($file);
    }

    /**
     * Parse the XML Document node for node and populate the model property with mapped values
     */
    protected function parseXml()
    {
        while ($this->parser->read()) {
            $this->setParentNodeName();
            $parentChildNodeName = $this->getParentChildNodeName();
            $this->doNodePreprocessing();

            // Get the mappings for this node or a combination of parent node name, a dot and this node's name
            $fields = $this->fileToSchemaMapping->get($this->parser->name) ??
                $this->fileToSchemaMapping->get($parentChildNodeName) ?? false;

            // If there are no mappings for any reason, continue to the next node
            if (empty($fields) || !($fields instanceof Collection) || $fields->isEmpty()) {
                $this->doNodePostprocessing();
                $this->setPreviousNodeInformation();
                continue;
            }

            // Parse the node based on the mappings
            $fields->each(function ($mappingAttributes, $setter) {
                // Defensiveness in case of bad mappings
                if (empty($setter) || !($mappingAttributes instanceof Collection)) {
                    $this->logger->log(Logger::WARNING, "Empty setter method or bad mappings", [
                        'setterMethod'      => $setter ?? null,
                        'mappingAttributes' => $mappingAttributes ?? null,
                    ]);

                    return true;
                }

                try {
                    $this->parseNode($mappingAttributes, $setter);
                } catch (Exception $e) {
                    $this->logger->log(Logger::ERROR, "Unable to parse XML node", [
                        'exception'         => $e->getMessage(),
                        'trace'             => $e->getTraceAsString(),
                        'setterMethod'      => $setter ?? null,
                        'mappingAttributes' => $mappingAttributes,
                    ]);
                }

                return true;
            });

            $this->doNodePostprocessing();
            $this->setPreviousNodeInformation();
        }
    }

    /**
     * If the we have traversed deeper into the XML then push the previous node name onto the end of the
     * $parentNodeNames Collection. If we have traversed shallower out of the XML, pop the last element off the end of
     * the $parentNodeNames Collection.
     */
    protected function setParentNodeName()
    {
        // Make sure the previousNodeName and previousNodeDepth properties are set, i.e. we have parsed the first node.
        if (!isset($this->previousNodeName, $this->previousNodeDepth)) {
            return;
        }

        // Traversing deeper: push
        if ($this->parser->depth > $this->previousNodeDepth) {
            $this->parentNodeNames->push($this->previousNodeName);
            return;
        }

        // Traversing shallower: pop
        if ($this->parser->depth < $this->previousNodeDepth) {
            $this->parentNodeNames->pop();
        }
    }

    /**
     * Set the node name and depth after parsing each XML node
     */
    protected function setPreviousNodeInformation()
    {
        $this->previousNodeName  = $this->parser->name;
        $this->previousNodeDepth = $this->parser->depth;
    }

    /**
     * Parse a single node within an XML scan output
     * NOTE: This method is called as a closure, passed to the Collection class's each method.
     * Returning true within the closure will continue the iteration over the Collection, returning false will break out
     * of the iteration, that's why we always return true in the below parseNode() method
     *
     * @param Collection $mappingAttributes
     * @param string $setter
     * @return bool
     * @throws ParserMappingException
     */
    protected function parseNode(Collection $mappingAttributes, string $setter)
    {
        $parser = $this->parser;
        if (!($mappingAttributes instanceof Collection)) {
            return true;
        }

        $attribute        = $mappingAttributes->get(self::MAP_ATTRIBUTE_XML_ATTRIBUTE, self::NODE_TEXT_VALUE_DEFAULT);
        $validationRules  = $mappingAttributes->get(self::MAP_ATTRIBUTE_VALIDATION);
        $modelClass       = $mappingAttributes->get(self::MAP_ATTRIBUTE_ENTITY_CLASS, '');

        // If validation rules is a Collection then there are related attributes we need to validate on this node to
        // make sure we are checking the right node
        if ($validationRules instanceof Collection) {
            // Separate the related attributes from the main attribute
            $mainRule = $validationRules->get(self::MAP_ATTRIBUTE_MAIN_VALIDATION);
            if (empty($mainRule)) {
                throw new ParserMappingException("No main attribute found in Collection");
            }

            // Defensiveness: Make sure we have an instance of a Collection beyond this point
            $relatedAttributes = $validationRules->get(self::MAP_ATTRIBUTE_RELATED_VALIDATION);
            if (!($relatedAttributes instanceof Collection) && is_array($relatedAttributes)) {
                $relatedAttributes = new Collection($relatedAttributes);
            }

            if (!($relatedAttributes instanceof Collection)) {
                $relatedAttributes = new Collection();
            }

            $validationRules = $mainRule;

            // Validate the related attributes by checking each one and creating a collection of related attributes that
            // failed validation
            /** @var Collection $failingRelatedAttributes */
            $failingRelatedAttributes = $relatedAttributes->reject(function ($validationRules, $attribute) {
                $attributeValue = $this->parser->getAttribute($attribute);
                return $this->isValidXmlValueOrAttribute($attribute, $validationRules, $attributeValue);
            });

            // If there are any related attributes that failed validation, we stop here and continue on to the next node
            if (!$failingRelatedAttributes->isEmpty()) {
                return true;
            }
        }
        
        // No attributes specified, so look for a value on the XML node
        if ($attribute === self::NODE_TEXT_VALUE_DEFAULT) {
            // Validate the XML node's text value
            if (!$this->isValidXmlValueOrAttribute($attribute, $validationRules, $this->parser->readInnerXml())) {
                return true;
            }

            // Set the value on the model and continue to the next iteration
            $this->setValueOnEntity($this->parser->readInnerXml(), $setter, $modelClass);
            return true;
        }

        // If the XML node does not have attributes at this point, skip the node because we only look for
        // attributes after this point
        if (!$parser->hasAttributes) {
            $this->logger->log(Logger::NOTICE, "Expected a node with attributes, got node without attributes", [
                'tagName'       => $this->parser->name ?? null,
                'attributeName' => $attribute ?? null,
            ]);
            return true;
        }

        $this->doAttributePreProcessing();

        // Get the attribute value
        $attributeValue = $this->getXmlNodeAttributeValue($attribute);
        if (!isset($attributeValue)) {
            return true;
        }

        // Validate the attribute value
        if (!$this->isValidXmlValueOrAttribute($attribute, $validationRules, $attributeValue)) {
            $this->logger->log(Logger::DEBUG, "XML node attribute failed validation", [
                'tagName'         => $this->parser->name ?? null,
                'attributeName'   => $attribute ?? null,
                'validationRules' => $validationRules,
                'attributeValue'  => $attributeValue ?? null,
            ]);

            return true;
        }

        // Set the value on the model and continue to the next iteration
        $this->setValueOnEntity($attributeValue, $setter, $modelClass);

        $this->doAttributePostProcessing();

        return true;
    }

    /**
     * Set a value from the XML on the relevant entity using the relevant setter method
     *
     * @param mixed $attributeValue
     * @param string $setter
     * @param string $entityClass
     * @return bool
     */
    protected function setValueOnEntity($attributeValue, string $setter, string $entityClass)
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
     * Get the value of an XML attribute from the current node
     * 
     * @param string $attribute
     * @return string
     */
    protected function getXmlNodeAttributeValue(string $attribute)
    {
        return $this->parser->getAttribute($attribute);
    }

    /**
     * Validate the value extracted from the XML
     *
     * @param string $attribute
     * @param $validationRules
     * @param mixed $value
     * @return bool
     */
    protected function isValidXmlValueOrAttribute(string $attribute, $validationRules, $value): bool
    {
        // No validation rules or value
        if (!isset($validationRules, $value)) {
            return false;
        }

        // Check if we are validating an IP address
        if ($validationRules === FILTER_FLAG_IPV4 || $validationRules === FILTER_FLAG_IPV6) {
            return !empty(filter_var($value, FILTER_VALIDATE_IP, $validationRules));
        }

        // Perform the validation
        $validation = $this->validatorFactory->make(
            [$attribute => $value],
            [$attribute => $validationRules]
        );

        return $validation->passes();
    }

    /**
     * Move a processed file into the processed directory
     *
     * @param File $file
     * @return bool
     */
    public function moveFileToProcessed(File $file)
    {
        // Empty or non-existent file
        if (empty($file) || !$this->fileSystem->exists($file->getPath())) {
            return false;
        }

        // Get the path where processed files should be moved to
        $processedFilePath = str_replace('scans/', 'scans/processed/', $file->getPath());
        $processedPath     = $this->fileSystem->dirname($processedFilePath);

        // Check if the directory exists and if not, create it
        $dirExists = true;
        if (!$this->fileSystem->exists($processedPath)) {
            $dirExists = $this->fileSystem->makeDirectory($processedPath, 0744, true);
        }

        // Directory creation probably failed?
        if (!$dirExists) {
            $this->logger->log(Logger::ERROR, "Failed to create directory for processed file", [
                'file'      => $file->getPath(),
                'user'      => $file->getUserId(),
                'workspace' => $file->getWorkspaceApp()->getWorkspaceId(),
            ]);
            return false;
        }

        // Attempt to move a file to it's processed directory location
        if (!$this->fileSystem->move($file->getPath(), $processedFilePath)) {
            $this->logger->log(Logger::ERROR, "Could not move processed file", [
                'file'      => $file->getPath(),
                'user'      => $file->getUserId(),
                'workspace' => $file->getWorkspaceApp()->getWorkspaceId(),
            ]);
            return false;
        }

        // Mark the file as processed and persist to the DB
        $file->setProcessed(true);
        $this->em->persist($file);
        $this->em->flush($file);

        return true;
    }

    /**
     * Get a Collection of files that could be parsed by this parser instance
     *
     * @return Collection
     */
    public function getUnprocessedFiles(): Collection
    {
        return $this->fileRepository->findUnprocessed();
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

        // Persist Asset via the bus to handle permissions etc and so that this can be used as a primary entity that has
        // been persisted to the DB
        if ($entity instanceof Asset) {
            $command = new CreateAsset($this->file->getId(), $entity);
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
     * @param array $getChildrenMethods
     */
    protected function mergeWithTemporaryCollectionAndPersist(
        string $entityClass, string $keyGetterMethod, array $findOneBy = [], array $getChildrenMethods = []
    )
    {
        // Make sure we have a valid class and getter method
        if (!isset($keyGetterMethod, $entityClass) || !class_exists($entityClass)) {
            $this->logger->log(Logger::ERROR, "Invalid method parameter(s)", [
                'keyGetterMethod'      => "Expected string",
                'keyGetterMethodValue' => $keyGetterMethods ?? null,
                'entityClass'          => "Expected string",
                'entityClassValue'     => $entityClass ?? null,
            ]);

            return;
        }

        // Make sure we have a valid entity
        /** @var AbstractEntity $entity */
        $entity = $this->entities->get($entityClass);
        if (!$this->isValidEntity($entity, $entityClass)) {
            $this->logger->log(Logger::ERROR, "A valid entity object with the given class name was not found", [
                'entityClass' => $entityClass ?? null,
            ]);

            return;
        }

        // Make sure we have a valid key to use on the temporary Collection
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
        })->each(function ($temporaryEntity) use ($entity, $entityClass, $findOneBy, $getChildrenMethods) {
            // Merge the contents of the temporary entity with the current entity to form a single array
            /** @var AbstractEntity $temporaryEntity */
            $temporaryEntity->setFromArray($entity->toArray(true));
            $this->callChildrenGetterMethods($temporaryEntity, $getChildrenMethods);

            // Create a new instance of $entityClass in the $this->entities Collection and add the entity that contains
            // the merged contents of the current entity and the temporary entity
            //$this->initialiseNewEntity($entityClass);
            $this->entities->offsetUnset($entityClass);
            $this->entities->put($entityClass, $temporaryEntity);

            // Populate the new entity instance with the merged contents of the current and temporary entities
            // Persist the populated entity
            $this->persistPopulatedEntity($entityClass, $findOneBy, '', true);
            return true;
        });
    }

    /**
     * Iterate over the child getter methods and refresh the related entities
     *
     * @param AbstractEntity $parentEntity
     * @param array $childGetterMethods
     */
    protected function callChildrenGetterMethods(AbstractEntity $parentEntity, array $childGetterMethods)
    {
        if (empty($childGetterMethods)) {
            return;
        }

        collect($childGetterMethods)->each(function ($getChildrenMethod) use ($parentEntity) {
            $this->refreshChildEntities($parentEntity, $getChildrenMethod);
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

        // Set the parent entity on the child entity
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
     * Store the related generic output on the vulnerability
     * @param string $attributeNameForKey
     * @param string $propertyName
     */
    protected function storeTemporaryRawData(string $attributeNameForKey, string $propertyName)
    {
        // Make sure we have an attribute name, whose value we will extract to use as a key
        if (empty($attributeNameForKey) || empty($propertyName) || !property_exists($this, $propertyName)) {
            return;
        }

        if (!($this->$propertyName instanceof Collection)) {
            $this->logger->log(Logger::ERROR, "Invalid temporary storage property", [
                'expectingClass' => Collection::class,
                'gotClass'       => is_object($this->$propertyName) ? get_class($this->$propertyName) : 'Not an object',
            ]);

            return;
        }

        // Check if there is something already stored at the key, but if not, create the key and store the value
        $currentValueAtKey = $this->$propertyName->get($this->parser->getAttribute($attributeNameForKey));
        if (empty($currentValueAtKey)) {
            $this->$propertyName->put(
                $this->parser->getAttribute($attributeNameForKey),
                $this->parser->readInnerXml()
            );

            return;
        }

        // There key exists, but the current value is the same as the contents of the current node so do nothing
        if ($this->parser->readInnerXml() == $currentValueAtKey) {
            return;
        }

        // Append the contents of the current node to the value that already exists at the key
        $this->$propertyName->put(
            $this->parser->getAttribute($attributeNameForKey),
            $currentValueAtKey . PHP_EOL . PHP_EOL . $this->parser->readInnerXml()
        );
    }

    /**
     * Extract raw XML data stored temporarily to be added to the entity of the given class
     *
     * @param string $entityClass
     * @param string $keyGetterMethod
     * @param string $propertyName
     * @param string $setterMethod
     */
    protected function addTemporaryRawDataToEntity(
        string $entityClass, string $keyGetterMethod, string $propertyName, string $setterMethod
    )
    {
        // Make sure the relevant temporary property exists as a Collection
        if (!property_exists($this, $propertyName) || !($this->$propertyName instanceof Collection)) {
            $this->logger->log(Logger::ERROR, "No Collection found at the given property name", [
                'entityClass'     => $entityClass ?? null,
                'keyGetterMethod' => $keyGetterMethod ?? null,
                'propertyName'    => $propertyName ?? null,
                'setterMethod'    => $setterMethod ?? null,
            ]);

            return;
        }

        // Get the relevant entity from the entities Collection and validate it.
        $entity = $this->entities->get($entityClass);
        if (!$this->isValidEntity($entity, $entityClass)) {
            $this->logger->log(Logger::ERROR, "A valid entity object with the given class name was not found", [
                'entityClass'     => $entityClass ?? null,
                'keyGetterMethod' => $keyGetterMethod ?? null,
                'propertyName'    => $propertyName ?? null,
                'setterMethod'    => $setterMethod ?? null,
            ]);

            return;
        }

        // Make sure the entity has the given setter method
        if (empty($setterMethod) || !method_exists($entity, $setterMethod)) {
            $this->logger->log(Logger::ERROR, "A valid entity object with the given class name was not found", [
                'entityClass'     => $entityClass ?? null,
                'keyGetterMethod' => $keyGetterMethod ?? null,
                'propertyName'    => $propertyName ?? null,
                'setterMethod'    => $setterMethod ?? null,
            ]);

            return;
        }

        // Make sure we have a valid key and that a value exists at the key
        $id = $this->getKeyValueForTemporaryCollection($entity, $keyGetterMethod);
        if (empty($id) || !$this->$propertyName->offsetExists($id)) {
            $this->logger->log(
                Logger::WARNING,
                "Could not get key value using entity getter method or there is no value at the key",
                [
                    'entityClass'     => $entityClass,
                    'keyGetterMethod' => $keyGetterMethod ?? null,
                    'propertyName'    => $propertyName ?? null,
                ]
            );

            return;
        }

        // Set the value from the value stored at the key on the entity
        $entity->$setterMethod(
            $this->$propertyName->get($id)
        );
    }

    /**
     * Set/add an entity on a parent entity without explicitly persisting it so that it can be cascade persisted
     *
     * @param string $entityClass
     * @param string $parentClass
     * @param array $findOneByCriteria
     */
    protected function addToParentForCascadePersist(string $entityClass, string $parentClass, array $findOneByCriteria)
    {
        // Get the relevant entity from the entities Collection and validate it.
        $entity = $this->entities->get($entityClass);
        if (!$this->isValidEntity($entity, $entityClass)) {
            $this->logger->log(Logger::ERROR, "A valid entity object with the given class name was not found", [
                'entityClass' => $entityClass ?? null,
            ]);

            return;
        }

        // Populate a criteria array with the values from the entity instance
        $findOneByCriteria = $this->getPopulatedCriteria($entity, $findOneByCriteria, null);

        // Check if the entity is managed or exists in the DB
        $entity = $this->checkForExistingEntity($findOneByCriteria, $entityClass, $entity);

        $this->setParentRelationship($entity, $entityClass, $parentClass);
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
        // Make sure we can get an ID from the parent entity
        $parentId = $this->getParentIdFromParentEntity($parentEntity);
        if ($parentId === null) {
            return $criteria;
        }

        // Make sure the ID column exists on the entity
        $idColumnName = $parentEntity::TABLE_NAME . '_id';
        if (!array_key_exists($idColumnName, $criteria)) {
            return $criteria;
        }

        // Add the parent to the criteria
        $criteria[$idColumnName] = $parentId;
        return $criteria;
    }

    /**
     * Get the ID (PRIMARY KEY) value of the parent entity
     *
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
     *
     * @param array $findOneByCriteria
     * @param string $entityClass
     * @param AbstractEntity $entity
     * @return object|AbstractEntity
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

        $uowEntity = $this->searchEntityManagerByHash($entityClass, $entity);
        if (!empty($uowEntity)) {
            return $uowEntity;
        }

        // Search for the entity in the database and if it exists, merge the given instance with the persistent instance
        $existingEntity = $this->em->getRepository($entityClass)->findOneBy($findOneByCriteria);
        if (!empty($existingEntity)) {
            $entity->setId($existingEntity->getId());
            $entity = $this->em->merge($entity);
            return $entity;
        }

        return $entity;
    }

    /**
     * Search the entire Doctrine UnitOfWork for an entity with the same unique hash value
     *
     * @param string $entityClass
     * @param AbstractEntity $entity
     * @return AbstractEntity|null
     */
    protected function searchEntityManagerByHash(string $entityClass, AbstractEntity $entity)
    {
        // If the entity doesn't implement the GeneratesUniqueHash contract, return the entity as is
        if (!($entity instanceof GeneratesUniqueHash)) {
            return null;
        }

        // Check if there are scheduled inserts in the Doctrine UnitOfWork, if not, return the entity as is
        $ouwEntities = collect($this->em->getUnitOfWork()->getIdentityMap())
            ->merge(collect($this->em->getUnitOfWork()->getScheduledEntityInsertions()))
            ->merge(collect($this->em->getUnitOfWork()->getScheduledEntityUpdates()));

        if ($ouwEntities->isEmpty()) {
            return null;
        }

        // See if an entity with same unique hash exists in the scheduled insertions
        $keyOfExisting = $ouwEntities->filter(function ($entity) use ($entityClass) {
            return $entity instanceof $entityClass && $entity instanceof GeneratesUniqueHash;
        })->map(function ($entity) {
            /** @var GeneratesUniqueHash $entity */
            return $entity->getHash();
        })->search($entity->getHash(), true);

        // Check if we got a key back and if not return null
        if ($keyOfExisting === false) {
            return null;
        }

        // Found a matching entity in the Doctrine UnitOfWork, return that instance to prevent duplicates
        return $ouwEntities->get($keyOfExisting);
    }

    /**
     * Add a file relation to persist to the many-to-many join table
     *
     * @param object $entity
     * @param string $entityClass
     * @param bool $addFileToEntity
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

        // Check if the entity has a file relationship
        if (empty($entity) || !($entity instanceof RelatesToFiles)) {
            return;
        }

        // Add the file to the entity where instructed by the the $addFileToEntity flag
        if ($addFileToEntity) {
            $entity->addFile($this->file);
            return;
        }

        // Get the method on the file entity that adds this entity as a relation
        $addToFileMethod = $this->getMethodNameToAddEntityFileRelation($entityClass);
        if (empty($addToFileMethod) || !method_exists($this->file, $addToFileMethod)) {
            $this->logger->log(Logger::ERROR, "Method to add file relation for this entity does not exist", [
                'addToFileMethod' => $addToFileMethod ?? null,
                'entityClass'     => $entityClass ?? null,
            ]);

            return;
        }

        // Add the entity to the file as a relation
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
            $this->file->getWorkspaceApp()->getWorkspace()->getUser()
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
     * Extract a CData field and set the value on the model, optionally appending the value
     *
     * @param string $entityClass
     * @param string $setter
     * @param string $heading
     * @param bool $append
     */
    protected function captureCDataField(
        string $entityClass, string $setter, string $heading = '', bool $append = false
    )
    {
        // Check if the base64 flag is set on the node
        $isBase64 = $this->checkForBase64Encoding();

        // Move into the CData node
        $this->parser->read();

        // Exit early is this is not a CData field
        if ($this->parser->nodeType !== XMLReader::CDATA) {
            return;
        }

        // Wrap the heading in <h3></h3> tags
        if (!empty($heading)) {
            $heading = '<h3>' . $heading . '</h3>' . PHP_EOL;
        }

        // Get the entity and validate it and the setter method
        $entity = $this->entities->get($entityClass);
        if (empty($entity) || !method_exists($entity, $setter)) {
            return;
        }

        // Check if we need to decode a base64 encoded string
        $value = $this->parser->value;
        if ($isBase64) {
            $value = base64_decode($this->parser->value);
        }

        // If we have an empty value then exit early
        if (empty($value)) {
            return;
        }

        $value = utf8_encode($value);

        // When the append flag is not set, set the heading and node contents
        if (!$append) {
            $entity->$setter($heading . $value);
            return;
        }

        // When the append flag is set, append the heading and node contents to the the value that exists
        $getter = 'g' . substr($setter, 1);
        $entity->$setter($entity->$getter() . PHP_EOL . $heading . $value);
    }

    /**
     * Check if the value is base64 encoded
     *
     * @return bool
     */
    protected function checkForBase64Encoding(): bool
    {
        return $this->parser->getAttribute('base64') === 'true';
    }

    /**
     * Execute the node pre-processing hooks
     */
    protected function doNodePreprocessing()
    {
        // Do post processing only on closing node tags or after empty nodes
        if ($this->parser->nodeType === XMLReader::END_ELEMENT) {
            return;
        }

        $this->callPreOrPostProcessingMethods($this->nodePreprocessingMap);
    }

    /**
     * Execute the node post-processing hooks
     */
    protected function doNodePostprocessing()
    {
        // Do post processing only on closing node tags or after empty nodes
        if ($this->parser->nodeType !== XMLReader::END_ELEMENT && !$this->parser->isEmptyElement) {
            return;
        }

        $this->callPreOrPostProcessingMethods($this->nodePostProcessingMap);
    }

    /**
     * Execute the attribute pre-processing hooks
     */
    protected function doAttributePreProcessing()
    {
        $this->callPreOrPostProcessingMethods($this->attributePreProcessingMap);
    }

    /**
     * Execute the attribute post-processing hooks
     */
    protected function doAttributePostProcessing()
    {
        $this->callPreOrPostProcessingMethods($this->attributePostProcessingMap);
    }

    /**
     * Handle single or multiple pre or post-processing methods for this node or attribute
     *
     * @param Collection $processingMap
     */
    protected function callPreOrPostProcessingMethods(Collection $processingMap)
    {
        // Get the pre or -post processing method Collection
        $parentChildNodeName = $this->getParentChildNodeName();
        $processingMethods   = $processingMap->get($this->parser->name) ?? $processingMap->get($parentChildNodeName);

        // Exit early if there are no processing methods
        if (empty($processingMethods)) {
            return;
        }

        // If we don't have a Collection we are executing a single method that has no parameters
        if (!($processingMethods instanceof Collection)) {
            $this->callPreOrPostProcessingMethod($processingMethods);
            return;
        }

        $processingMethods->each(function ($parameters, $method) {
            // For elements in the Collection that have only a method and no parameters, the method will end up in
            // $parameters, so we need to handle this scenario
            if (!is_string($method) && is_string($parameters)) {
                $method     = $parameters;
                $parameters = null;
            }

            // Process each hook
            $this->callPreOrPostProcessingMethod($method, $parameters);
            return true;
        });
    }

    /**
     * Call the pre or post-processing method for this node or attribute
     *
     * @param string $processingMethod
     * @param null $parameters
     * @throws Exception
     */
    protected function callPreOrPostProcessingMethod(string $processingMethod, $parameters = null)
    {
        // Make sure we have a valid processing method
        if (empty($processingMethod) || !method_exists($this, $processingMethod)) {
            $this->logger->log(Logger::ERROR, "Pre or Post-processing method does not exist", [
                'processingMethodName' => $processingMethod ?? null,
                'xmlNodeName'          => $this->parser->name ?? null,
            ]);

            return;
        }

        try {
            // Process methods that have no parameters
            if (empty($parameters) || !($parameters instanceof Collection)) {
                $this->$processingMethod();
                return;
            }

            // Process method that have parameters
            call_user_func_array([$this, $processingMethod], $parameters->all());
        } catch (Exception $e) {
            // Catch any exceptions to log the error in this context and then re-throw the exception
            $this->logger->log(Logger::ERROR, "Error when call pre or post-processing method", [
                'processingMethodName' => $processingMethod,
                'xmlNodeName'          => $this->parser->name ?? null,
                'methodParameters'     => $parameters,
                'exception'            => $e->getMessage(),
                'exceptionTrace'       => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Call the pre or post-processing method for this node or attribute with parameters
     *
     * @param Collection $methodAndParameters
     * @throws Exception
     */
    protected function callPreOrPostProcessingMethodWithParameters(Collection $methodAndParameters)
    {
        // Make sure we have a Collection which will be the method as the key and a Collection of parameters
        if ($methodAndParameters->isEmpty()) {
            $this->logger->log(Logger::NOTICE, "Empty processing map Collection" ,[
                'xmlNodeName' => $this->parser->name ?? null,
            ]);

            return;
        }

        // Extract the method and parameters separately from the Collection
        $processingMethod    = $methodAndParameters->get(self::MAP_ATTRIBUTE_HOOK_METHOD);
        $methodAndParameters = $methodAndParameters->get(self::MAP_ATTRIBUTE_HOOK_METHOD_PARAMETERS);

        // Make sure we have a valid method
        if (empty($processingMethod) || !method_exists($this, $processingMethod)) {
            $this->logger->log(Logger::ERROR, "Pre or Post-processing method does not exist", [
                'processingMethodName' => $processingMethod ?? null,
                'xmlNodeName'          => $this->parser->name ?? null,
            ]);

            return;
        }

        // Make sure we have valid method parameters
        if (empty($methodAndParameters) || !($methodAndParameters instanceof Collection)) {
            $this->logger->log(Logger::ERROR, "No valid pre or post-processing method parameters found", [
                'processingMethodName' => $processingMethod ?? null,
                'xmlNodeName'          => $this->parser->name ?? null,
                'methodParameters'     => $methodAndParameters ?? null,
            ]);

            return;
        }

        // Convert the parameters to an array
        $methodAndParameters = $methodAndParameters->toArray();
        try {
            // Call the method with the parameters
            call_user_func_array([$this, $processingMethod], $methodAndParameters);
        } catch (Exception $e) {
            // Catch any exceptions to log the error in this context and then re-throw the exception
            $this->logger->log(Logger::ERROR, "Error when call pre or post-processing method with parameters", [
                'processingMethodName' => $processingMethod,
                'xmlNodeName'          => $this->parser->name ?? null,
                'methodParameters'     => $methodAndParameters,
                'exception'            => $e->getMessage(),
                'exceptionTrace'       => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Flush the current Doctrine Unit of Work
     */
    protected function flushDoctrineUnitOfWork()
    {
        $this->em->flush();
    }

    /**
     * Refresh the Collection of temporary entities with the managed entities that have IDs (PRIMARY KEY values)
     */
    protected function refreshTemporaryEntities()
    {
        // Check that we have some temporary entities
        if ($this->temporaryEntities->isEmpty()) {
            return;
        }

        // Replace entities with persistent versions where possible
        $this->temporaryEntities->merge(
            $this->temporaryEntities->collapse()->filter(function ($entity) {
                return $entity instanceof GeneratesUniqueHash;
            })->map(function ($entity) {
                /** @var GeneratesUniqueHash $entity */
                $existingEntity = $this->em->getRepository(get_class($entity))
                    ->findOneBy($entity->getUniqueKeyColumns()->all());
                return $existingEntity ?? $entity;
            })
        );
    }

    /**
     * Get the short class name from a FQCN (Fully Qualified Class Name)
     *
     * @param string $fqcn
     * @return null|string
     */
    protected function getEntityShortClassName(string $fqcn)
    {
        if (empty($fqcn) || !class_exists($fqcn)) {
            return null;
        }

        $reflection = new ReflectionClass($fqcn);
        return $reflection->getShortName();
    }

    /**
     * Get the temporary property name for the entity
     *
     * @param string $entityClass
     * @return null
     */
    protected function getEntityPropertyName(string $entityClass)
    {
        $shortClassName = $this->getEntityShortClassName($entityClass);

        return isset($shortClassName) ? lcfirst($shortClassName) : null;
    }

    /**
     * Get a dot syntax representation in the format parentNodeName.currentNodeName
     *
     * @return string
     */
    protected function getParentChildNodeName(): string
    {
        return $this->parentNodeNames->last() . '.' . $this->parser->name;
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
        $this->setValueOnEntity($attributeValues, $setter, $entityClass);
    }

    /**
     * Check the current authenticated User is the same as the User who uploaded the file and if not, authenticate the
     * User who uploaded the file so that all permissions for all operations are checked against that user
     *
     * @param File $file
     */
    protected function getAndAuthenticateFileUser(File $file)
    {
        $currentUser = Auth::user();
        /** @var User $fileUser */
        $fileUser    = $file->getUser();

        // If the authenticated User is the same as the User that uploaded the file, there is nothing to do
        if (!empty($currentUser) && $currentUser instanceof User && $currentUser->getId() === $fileUser->getId()) {
            return;
        }

        // Login the User that uploaded the file
        Auth::login($fileUser);
    }

    /**
     * Get the name of the base tag used to identify the start of a new asset
     *
     * @return string
     */
    abstract protected function getBaseTagName();

    /**
     * Get the storage path for the relevant XML files 
     * 
     * @return string
     */
    abstract public function getStoragePath(): string;

    /**
     * @return XMLReader
     */
    public function getParser()
    {
        return $this->parser;
    }

    /**
     * @return Filesystem
     */
    public function getFileSystem()
    {
        return $this->fileSystem;
    }

    /**
     * @return Factory
     */
    public function getValidatorFactory()
    {
        return $this->validatorFactory;
    }

    /**
     * @return FileRepository
     */
    public function getFileRepository()
    {
        return $this->fileRepository;
    }

    /**
     * @return EntityManager
     */
    public function getEm()
    {
        return $this->em;
    }

    /**
     * @return JsonLogService
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @inheritdoc
     */
    public function setLoggerContext(JsonLogService $logger)
    {
        $directory = $this->getLogContext();
        $logger->setLoggerName($directory);

        $filename  = $this->getLogFilename();
        $logger->setLogFilename($filename);
    }

    /**
     * @inheritdoc
     */
    public function getLogContext(): string
    {
        return 'services';
    }

    /**
     * @inheritdoc
     */
    public function getLogFilename(): string
    {
        return 'xml-parser.json.log';
    }

    /**
     * @return Collection
     */
    public function getFileToSchemaMapping(): Collection
    {
        return $this->fileToSchemaMapping;
    }
}