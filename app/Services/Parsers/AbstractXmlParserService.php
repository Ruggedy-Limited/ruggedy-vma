<?php

namespace App\Services\Parsers;

use App\Contracts\CollectsScanOutput;
use App\Contracts\CustomLogging;
use App\Contracts\ParsesXmlFiles;
use App\Entities\File;
use App\Exceptions\ParserMappingException;
use App\Repositories\FileRepository;
use App\Services\JsonLogService;
use Doctrine\ORM\EntityManager;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Validation\Factory;
use Monolog\Logger;
use XMLReader;

abstract class AbstractXmlParserService implements ParsesXmlFiles, CustomLogging
{
    /** Map attributes */
    const MAP_ATTRIBUTE_XML_ATTRIBUTE      = 'xmlAttribute';
    const MAP_ATTRIBUTE_VALIDATION         = 'validation';
    const MAP_ATTRIBUTE_MAIN_VALIDATION    = 'main';
    const MAP_ATTRIBUTE_RELATED_VALIDATION = 'related';

    /** Attribute fallback value for validating & extracting the inner text value of a node */
    const NODE_TEXT_VALUE_DEFAULT = 'nodeTextValue';

    /** Match any XML tag */
    const REGEX_ANY_XML_TAG = '%((<.+(/)?>)|((<.+>)(.*)?(</.+>)?))%mi';

    /** @var XMLReader */
    protected $parser;

    /** @var Filesystem */
    protected $fileSystem;

    /** @var Factory */
    protected $validatorFactory;

    /** @var FileRepository */
    protected $fileRepository;

    /** @var EntityManager */
    protected $em;

    /** @var JsonLogService */
    protected $logger;
    
    /** @var Collection */
    protected $fileToSchemaMapping;

    /** @var CollectsScanOutput */
    protected $model;

    /** @var Collection */
    protected $models;

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
        XMLReader $parser, Filesystem $fileSystem, Factory $validatorFactory, FileRepository $fileRepository,
        EntityManager $em, JsonLogService $logger
    )
    {
        $this->parser           = $parser;
        $this->fileSystem       = $fileSystem;
        $this->validatorFactory = $validatorFactory;
        $this->fileRepository   = $fileRepository;
        $this->em               = $em;

        $this->setLoggerContext($logger);
        $this->logger = $logger;

        $this->fileToSchemaMapping = new Collection();
        $this->models              = new Collection();
    }

    /**
     * Parse an XML file and return the relevant model or false on failure
     *
     * @param File $file
     * @return Collection
     * @throws FileNotFoundException
     */
    public function processXmlFile(File $file): Collection
    {
        if (!isset($file)) {
            return new Collection();
        }

        // Check that the file exists
        if (!$this->getFileSystem()->exists($file->getPath())) {
            throw new FileNotFoundException("The given file was not found in the default location");
        }

        // Attempt to parse the XML and catch any exceptions
        $parser = $this->getParser();
        try {
            $parser->open($file->getPath());
            $this->parseXml();
        } catch (Exception $e) {
            $this->getLogger()->log(Logger::ERROR, "Failed to parse XML file.", [
                'file'      => $file->getPath(),
                'user'      => $file->getUserId(),
                'workspace' => $file->getWorkspaceId(),
                'exception' => $e->getMessage(),
                'trace'     => $this->getLogger()->getTraceAsArrayOfLines($e),
            ]);

            return new Collection;
        }

        // Return a populated model
        return $this->getModels();
    }

    /**
     * Parse the XML Document node for node and populate the model property with mapped values
     */
    protected function parseXml()
    {
        while ($this->getParser()->read()) {
            // End tag for the base tag, add the current model to the models Collection and reset the model
            if ($this->getParser()->nodeType === XMLReader::END_ELEMENT
                && $this->getParser()->name === $this->getBaseTagName()) {
                $this->models->push($this->getModel());
                $this->resetModel();
                continue;
            }

            // Any other end tag
            if ($this->getParser()->nodeType === XMLReader::END_ELEMENT) {
                continue;
            }

            // Get the mappings
            $fields = $this->getFileToSchemaMapping()->get($this->getParser()->name);
            if (empty($fields) || !($fields instanceof Collection)) {
                continue;
            }

            // Parse the node based on the mappings
            $fields->each(function($mappingAttributes, $setter) {
                if (empty($setter) || !($mappingAttributes instanceof Collection)) {
                    return true;
                }
                
                return $this->parseNode($mappingAttributes, $setter);
            });
        }
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
        $parser = $this->getParser();
        $getter = 'g' . substr($setter, 1);
        if (!method_exists($this->getModel(), $setter) || !($mappingAttributes instanceof Collection)) {
            return true;
        }

        $attribute         = $mappingAttributes->get(self::MAP_ATTRIBUTE_XML_ATTRIBUTE, self::NODE_TEXT_VALUE_DEFAULT);
        $validationRules   = $mappingAttributes->get(self::MAP_ATTRIBUTE_VALIDATION);

        // If validation rules is a Collection then there are related attributes we need to validate on this node to
        // make sure we are checking the right node
        if ($validationRules instanceof Collection) {
            // Separate the related attributes from the main attribute
            $mainRule = $validationRules->get(self::MAP_ATTRIBUTE_MAIN_VALIDATION);
            if (empty($mainRule)) {
                throw new ParserMappingException("No main attribute found in Collection");
            }

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
                $attributeValue = $this->getParser()->getAttribute($attribute);
                return $this->isValidXmlValueOrAttribute($attribute, $validationRules, $attributeValue);
            });

            // If there are any related attributes that failed validation, we stop here and continue on to the next node
            if (!$failingRelatedAttributes->isEmpty()) {
                return true;
            }
        }
        
        // No attributes specified, so look for a value on the XML node
        if ($attribute === self::NODE_TEXT_VALUE_DEFAULT && !empty($parser->readInnerXml())) {
            $nodeInnerXml = $parser->readInnerXml();
            // The node contains tags
            if (preg_match(self::REGEX_ANY_XML_TAG, $nodeInnerXml)) {
                return true;
            }

            // Validate the XML node's text value
            if (!$this->isValidXmlValueOrAttribute($attribute, $validationRules, $nodeInnerXml)) {
                return true;
            }

            // Set the value on the model
            $this->getModel()->$setter($nodeInnerXml);
            return true;
        }

        // If the XML node does not have attributes at this point, skip the node because we only look for
        // attributes after this point
        if (!$parser->hasAttributes) {
            return true;
        }

        // Get the attribute value
        $attributeValue = $this->getXmlNodeAttributeValue($attribute);
        if (!isset($attributeValue)) {
            return true;
        }

        if (!$this->isValidXmlValueOrAttribute($attribute, $validationRules, $attributeValue)) {
            return true;
        }

        $this->getModel()->$setter($attributeValue);
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
        return $this->getParser()->getAttribute($attribute);
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
        if (!isset($validationRules, $value)) {
            return false;
        }

        if ($validationRules === FILTER_FLAG_IPV4 || $validationRules === FILTER_FLAG_IPV6) {
            return !empty(filter_var($value, FILTER_VALIDATE_IP, $validationRules));
        }

        $validation = $this->getValidatorFactory()->make(
            [$attribute => $value],
            [$attribute => $validationRules]
        );

        $isValid = $validation->passes();

        return $isValid;
    }

    /**
     * Move a processed file into the processed directory
     *
     * @param File $file
     * @return bool
     */
    public function moveFileToProcessed(File $file)
    {
        if (empty($file) || !$this->getFileSystem()->exists($file->getPath())) {
            return false;
        }

        $processedFilePath = str_replace('scans/', 'scans/processed/', $file->getPath());
        $processedPath     = $this->getFileSystem()->dirname($processedFilePath);

        $dirExists = true;
        if (!$this->getFileSystem()->exists($processedPath)) {
            $dirExists = $this->getFileSystem()->makeDirectory($processedPath, 0744, true);
        }

        if (!$dirExists) {
            $this->getLogger()->log(Logger::ERROR, "Failed to create directory for processed file", [
                'file'      => $file->getPath(),
                'user'      => $file->getUserId(),
                'workspace' => $file->getWorkspaceId(),
            ]);
            return false;
        }
        
        if (!$this->getFileSystem()->move($file->getPath(), $processedFilePath)) {
            $this->getLogger()->log(Logger::ERROR, "Could not move processed file", [
                'file'      => $file->getPath(),
                'user'      => $file->getUserId(),
                'workspace' => $file->getWorkspaceId(),
            ]);
            return false;
        }

        // Mark the file as processed in the database
        $file->setProcessed(true);
        $this->getEm()->persist($file);
        $this->getEm()->flush($file);

        return true;
    }

    /**
     * Get a Collection of files that could be parsed by this parser instance
     *
     * @return Collection
     */
    public function getParseableFiles(): Collection
    {
        return $this->getFileRepository()->findUnprocessed();
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

    /**
     * @return Collection
     */
    public function getModels()
    {
        return $this->models;
    }

    /**
     * Reset the models property to a new, empty Collection
     */
    public function resetModels()
    {
        $this->models = new Collection();
    }
}