<?php

namespace App\Services\Parsers;

use App\Contracts\CollectsScanOutput;
use App\Contracts\CustomLogging;
use App\Contracts\GeneratesUniqueHash;
use App\Contracts\ParsesXmlFiles;
use App\Entities\Asset;
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
use Doctrine\ORM\EntityManager;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Factory;
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

    /** @var CollectsScanOutput */
    protected $model;

    /** @var Collection */
    protected $entityRelationshipSetterMap;

    /**
     * NOTES: My new idea is to have a Collection of Asset entities only once the parsing of a scan is complete. As we
     * parse the data and we get Asset data, we set the data in the $entities Collection at the Asset class name offset
     * which will be an Asset entity.
     * We add a hook at the relevant point in the scan where we will have collected all possible Asset data,
     * at which point we will do a findOneBy to see if an Asset with the same hostname and IP combination exists.
     * If it does exist will will set the ID on and merge the entity from the Collection. If not, we will just persist
     * the Asset.
     * and if not, add the entity to the Collection, using the hash as a key, and then set the $currentAssetHash
     * property to the hash. If the hash already exists, just set the $currentAssetHash property to the hash.
     * As we parse the data in the file we add the data to the relevant temp property, e.g. $tempVulnerability,
     * $tempOpenPort, $tempSoftwareInformation, $tempVulnerabilityReference, $tempExploit.
     * Add hooks at the relevant points in the scan to add these related entities to Asset as relations.
     * Override the method that adds related Vulnerabilities relations to the Asset entity to use the a key, e.g.
     * CVE or vulnerability ID or a hash where there isn't anything specific in the scan.
     */

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
     * @param FileRepository $fileRepository
     * @param EntityManager $em
     * @param JsonLogService $logger
     */
    public function __construct(
        XMLReader $parser, Filesystem $fileSystem, Factory $validatorFactory, AssetRepository $assetRepository,
        FileRepository $fileRepository, EntityManager $em, JsonLogService $logger
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

        $this->parentNodeNames = new Collection();

        $this->fileToSchemaMapping        = new Collection();
        $this->nodePreprocessingMap       = new Collection();
        $this->nodePostProcessingMap      = new Collection();
        $this->attributePreProcessingMap  = new Collection();
        $this->attributePostProcessingMap = new Collection();

        $this->entities           = new Collection();
        $this->temporaryEntities  = new Collection();

        $this->entityRelationshipSetterMap = new Collection([
            Workspace::class           => new Collection([
                Asset::class => 'addAsset',
            ]),
            Asset::class               => new Collection([
                Vulnerability::class       => 'addVulnerability',
                OpenPort::class            => 'addOpenPort',
                SoftwareInformation::class => 'addSoftwareInformation'
            ]),
            Exploit::class             => new Collection([
                Vulnerability::class => 'addVulnerability',
            ]),
            Vulnerability::class       => new Collection([
                Asset::class                      => 'addAsset',
                VulnerabilityReferenceCode::class => 'addVulnerabilityReferenceCode',
                Exploit::class                    => 'addExploit',
            ]),
            VulnerabilityReferenceCode::class => new Collection([
                Vulnerability::class => 'setVulnerability',
            ]),
            OpenPort::class            => new Collection([
                Asset::class => 'setAsset',
            ]),
            SoftwareInformation::class => new Collection([
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
        $this->entities->put(Workspace::class, $file->getWorkspace());

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
                'workspace' => $file->getWorkspaceId(),
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
        $modelClass       = $mappingAttributes->get(self::MAP_ATTRIBUTE_ENTITY_CLASS);

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
            /* If we are on the start element, read into the node text.
            if ($parser->nodeType === XMLReader::ELEMENT) {
                $parser->read();
            }

            $value = $this->parser->value;

            // If we have not hit a TEXT or CDATA node here, get the current node's raw outer XML
            if ($parser->nodeType !== XMLReader::TEXT && $parser->nodeType !== XMLReader::CDATA) {

                $this->parser->moveToElement()
                $value = $this->parser->readOuterXml();

                $this->logger->log(
                    Logger::NOTICE, "Expected a text or cdata node, but got a {$parser->nodeType} node",
                    [
                        'nodeName'      => $this->parser->name ?? null,
                        'attributeName' => $attribute ?? null,
                        'setter'        => $setter ?? null,
                    ]
                );
            }*/

            // Validate the XML node's text value
            if (!$this->isValidXmlValueOrAttribute($attribute, $validationRules, $this->parser->readInnerXml())) {
                return true;
            }

            // Set the value on the model and continue to the next iteration
            $this->setValueOnModel($this->parser->readInnerXml(), $setter, $modelClass);
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
        $this->setValueOnModel($attributeValue, $setter, $modelClass);

        $this->doAttributePostProcessing();

        return true;
    }

    /**
     * Set the value of an attribute on the model
     *
     * @param mixed $attributeValue
     * @param int|string $setter
     * @param string $entityClass
     */
    protected function setValueOnModel($attributeValue, string $setter, string $entityClass = '')
    {
        $this->model->$setter($attributeValue);
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
                'workspace' => $file->getWorkspaceId(),
            ]);
            return false;
        }

        // Attempt to move a file to it's processed directory location
        if (!$this->fileSystem->move($file->getPath(), $processedFilePath)) {
            $this->logger->log(Logger::ERROR, "Could not move processed file", [
                'file'      => $file->getPath(),
                'user'      => $file->getUserId(),
                'workspace' => $file->getWorkspaceId(),
            ]);
            return false;
        }

        // Mark the file as processed and persist to the DB
        // TODO: Move the out into a command
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
        $parentChildNodeName = $this->getParentChildNodeName();
        $processingMethods   = $processingMap->get($this->parser->name) ?? $processingMap->get($parentChildNodeName);

        if (empty($processingMethods)) {
            return;
        }

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
        if (empty($processingMethod) || !method_exists($this, $processingMethod)) {
            $this->logger->log(Logger::ERROR, "Pre or Post-processing method does not exist", [
                'processingMethodName' => $processingMethod ?? null,
                'xmlNodeName'          => $this->parser->name ?? null,
            ]);

            return;
        }

        try {
            if (empty($parameters) || !($parameters instanceof Collection)) {
                $this->$processingMethod();
                return;
            }

            call_user_func_array([$this, $processingMethod], $parameters->all());
        } catch (Exception $e) {
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
        if ($methodAndParameters->isEmpty()) {
            $this->logger->log(Logger::NOTICE, "Empty processing map Collection" ,[
                'xmlNodeName' => $this->parser->name ?? null,
            ]);

            return;
        }

        $processingMethod    = $methodAndParameters->get(self::MAP_ATTRIBUTE_HOOK_METHOD);
        $methodAndParameters = $methodAndParameters->get(self::MAP_ATTRIBUTE_HOOK_METHOD_PARAMETERS);

        if (empty($processingMethod) || !method_exists($this, $processingMethod)) {
            $this->logger->log(Logger::ERROR, "Pre or Post-processing method does not exist", [
                'processingMethodName' => $processingMethod ?? null,
                'xmlNodeName'          => $this->parser->name ?? null,
            ]);

            return;
        }

        if (empty($methodAndParameters) || !($methodAndParameters instanceof Collection)) {
            $this->logger->log(Logger::ERROR, "No valid pre or post-processing method parameters found", [
                'processingMethodName' => $processingMethod ?? null,
                'xmlNodeName'          => $this->parser->name ?? null,
                'methodParameters'     => $methodAndParameters ?? null,
            ]);

            return;
        }

        $methodAndParameters = $methodAndParameters->toArray();
        try {
            call_user_func_array([$this, $processingMethod], $methodAndParameters);
        } catch (Exception $e) {
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
        if ($this->temporaryEntities->isEmpty()) {
            return;
        }

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
     * Check the current authenticated User is the same as the User who uploaded the file and if not, authenticate the
     * User who uploaded the file so that all permissions for all operations are checked against that user
     *
     * @param File $file
     */
    protected function getAndAuthenticateFileUser(File $file)
    {
        $currentUser = Auth::user();
        $fileUser    = $file->getUser();
        if (!empty($currentUser) && $currentUser instanceof User && $currentUser->getId() === $fileUser->getId()) {
            return;
        }

        Auth::login($fileUser);
    }

    /**
     * Reset the the model instance to a new instance
     */
    abstract protected function resetModel();

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
    function setLoggerContext(JsonLogService $logger)
    {
        $directory = $this->getLogContext();
        $logger->setLoggerName($directory);

        $filename  = $this->getLogFilename();
        $logger->setLogFilename($filename);
    }

    /**
     * @inheritdoc
     */
    function getLogContext(): string
    {
        return 'services';
    }

    /**
     * @inheritdoc
     */
    function getLogFilename(): string
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

    /**
     * @return CollectsScanOutput
     */
    public function getModel()
    {
        return $this->model;
    }
}