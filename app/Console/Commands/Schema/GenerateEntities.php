<?php

namespace App\Console\Commands\Schema;

use App\Entities\Base\AbstractEntity;
use Doctrine\ORM\EntityManager;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use RuntimeException;
use stdClass;
use Symfony\Component\Process\Process;

/**
 * Class GenerateEntities
 * @package App\Console\Commands
 */
class GenerateEntities extends Command
{
    const CONSTANT_DECLARATION     = "    const %s%s = '%s';";
    const TABLE_CONSTANT_COMMENT   = "    /** Table name constant */";
    const TABLE_NAME_CONSTANT_NAME = 'TABLE_NAME';
    const COLUMN_CONSTANT_COMMENT  = "    /** Column name constants */";

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:entities';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Doctrine 2 entities from a MySQL Workbench schema file';

    /** @var string The location of the mysql-workbench-schema-export config file relative to the root */
    protected $config        = 'schema/schema.json';

    /** @var string The location of the MySQL Workbench schema file relative to the root */
    protected $workbenchFile = 'schema/schema.mwb';

    /** @var string The command to execute the mysql-workbench-schema-export */
    protected $exporterBin   = 'vendor/bin/mysql-workbench-schema-export';

    /** @var EntityManager */
    protected $em;

    /**
     * Create a new command instance.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    /**
     * Execute the console command.
     * 
     * @return mixed
     * @throws Exception
     * @throws RuntimeException
     */
    public function handle()
    {
        // Check for the mysql-workbench-schema-export binary
        if (!file_exists(base_path($this->exporterBin))) {
            throw new Exception('Missing dependency - please run `composer update`');
        }

        // Check for the configuration file
        if (!file_exists(base_path($this->config))) {
            throw new Exception("Configuration file missing from {$this->config}");
        }

        // Check for the MySQL Workbench schema file
        if (!file_exists(base_path($this->workbenchFile))) {
            throw new Exception("Workbench file missing from {$this->workbenchFile}");
        }

        // Construct and execute the command
        $cmd = "php {$this->exporterBin} --config={$this->config} {$this->workbenchFile}";
        $process = new Process($cmd, base_path());
        $process->run();

        $this->info($process->getOutput());

        // If the command failed throw an exception
        if (!$process->isSuccessful()) {
            throw new RuntimeException($process->getErrorOutput());
        }

        // Return the command output
        $this->info($process->getOutput());

        $generateConstantsResult = $this->generateColumnAndTableNameClassConstants();
        if (empty($generateConstantsResult)) {
            $this->warn("Failed to generate class constants for entities");
            return;
        }

        $this->info("Successfully generated class constants for entities");
    }

    /**
     * Generate table and column name class constants for each generated Doctrine entity file
     *
     * @return bool
     */
    protected function generateColumnAndTableNameClassConstants()
    {
        // Get the doctrine generator config
        $configContents = file_get_contents(base_path($this->config));
        if (empty($configContents)) {
            return false;
        }

        // Decode the JSON config
        $decodedOptions = json_decode($configContents);
        if (empty($decodedOptions) || !($decodedOptions instanceof stdClass) || empty($decodedOptions->dir)) {
            return false;
        }

        // Make sure we have a valid base directory for entities
        $basePath = base_path($decodedOptions->dir);
        if (!is_dir($basePath)) {
            return false;
        }

        // Make sure we have the namespace information we need for entities
        if (empty($decodedOptions->params->bundleNamespace) || empty($decodedOptions->params->entityNamespace)) {
            return false;
        }

        // Fully qualified namespace for entities and absolute base path
        $entityFqns  = $decodedOptions->params->bundleNamespace . "\\" . $decodedOptions->params->entityNamespace . "\\";
        $entityFiles = scandir($basePath);

        // Make sure there are files in the directory
        if (empty($entityFiles)) {
            return false;
        }

        // Iterate over all the base entity files
        foreach ($entityFiles as $entityFile) {
            $fullPath = $basePath . DIRECTORY_SEPARATOR . $entityFile;
            if (!file_exists($fullPath) || !is_file($fullPath)) {
                continue;
            }

            // Get the entity name from the filename
            $entityName  = str_replace(".php", "", basename($entityFile));
            if (empty($entityName) || $entityName === 'AbstractEntity') {
                continue;
            }

            // Get the entity class and make sure it exists
            $entityClass = $entityFqns . $entityName;
            if (!class_exists($entityClass)) {
                continue;
            }

            // Get the column names from the entity class using the Doctrine EntityManager
            /** @var AbstractEntity $entity */
            $entity      = new $entityClass();
            $columnNames = array_keys($entity->toArray());

            // Generate a Collection of class constants for table column names
            $constants = $this->generateClassConstantsFromProperties($columnNames);
            if ($constants->isEmpty()) {
                continue;
            }

            // Get the contents of the entity class file
            $fileContents = file_get_contents($fullPath);
            if (empty($fileContents)) {
                continue;
            }

            // Generate the table name constant and concatenate with the column name constants to create one code block
            $tableNameConstant = $this->getTableNameConstantDeclaration($entityClass);
            $constantsDeclaration = $tableNameConstant . self::COLUMN_CONSTANT_COMMENT . PHP_EOL
                . $constants->reduce(function($carry, $constant) {
                return $carry .= $constant;
            });

            // Add the constants to the file contents
            $newFileContents = preg_replace(
                "/^class [A-Za-z0-9_\-]+( extends [A-Za-z0-9_\\\-]+)?(\n|\r\n)\{(\n|\r\n)/mi",
                "$0" . $constantsDeclaration . PHP_EOL,
                $fileContents
            );

            if (empty($newFileContents)) {
                continue;
            }

            // For some reason this annotation gets generated incorrectly when regenerating entities, fix it
            $fixedFileContents = str_replace(" * @ORM\\Entity()", " * @ORM\\MappedSuperclass", $newFileContents);

            // Update the file
            $result = file_put_contents($fullPath, $fixedFileContents);
            if (empty($result)) {
                $this->error("Failed to import column and table name class constants for file: $fullPath");
                continue;
            }

            $this->info("Successfully imported column and table name class constants for file: $fullPath");
        }

        return true;
    }

    /**
     * Generate column name class constants for a Doctrine entity class
     *
     * @param array $properties
     * @return Collection
     */
    protected function generateClassConstantsFromProperties(array $properties): Collection
    {
        if (empty($properties)) {
            return new Collection([]);
        }

        // Convert the array of properties into a Collection and get the length of the longest column name
        $properties = new Collection($properties);
        $maxNameLength = $properties->reduce(function ($carry, $propertyName) {
            $nameLength = strlen($propertyName);
            return $nameLength > $carry ? $nameLength : $carry;
        });

        // Generate the constants after filtering out created_at and updated_at which are common to all entities
        // that we have created and for which constants are defined in the AbstractEntity class
        $constants = $properties->filter(function ($propertyName, $offset) {
            return !in_array(
                $propertyName,
                [AbstractEntity::ID, AbstractEntity::CREATED_AT, AbstractEntity::UPDATED_AT]
            );
        })->map(function ($propertyName, $offset) use ($maxNameLength) {
            $nameLengthDiff = $maxNameLength - strlen($propertyName);
            $spacing = '';
            if (!empty($nameLengthDiff)) {
                $spacing = str_repeat(" ", $nameLengthDiff);
            }

            return sprintf(self::CONSTANT_DECLARATION, strtoupper($propertyName), $spacing, $propertyName) . PHP_EOL;
        });

        return $constants;
    }

    /**
     * Generate a TABLE_NAME class constant for a Doctrine entity class
     *
     * @param string $entityClass
     * @return string
     */
    protected function getTableNameConstantDeclaration(string $entityClass): string
    {
        // Match the table name in the Doctrine annotation above the class declaration
        $tableName = $this->em->getClassMetadata($entityClass)->getTableName();
        if (empty($tableName)) {
            return '';
        }

        return self::TABLE_CONSTANT_COMMENT . PHP_EOL
            . sprintf(self::CONSTANT_DECLARATION, self::TABLE_NAME_CONSTANT_NAME, '', $tableName) . PHP_EOL . PHP_EOL;
    }
}
