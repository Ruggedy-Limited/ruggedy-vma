<?php

namespace App\Console\Commands\Schema;

use App\Entities\Base\AbstractEntity;
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

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
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
        $configContents = file_get_contents(base_path($this->config));
        if (empty($configContents)) {
            return false;
        }

        $decodedOptions = json_decode($configContents);
        if (empty($decodedOptions) || !($decodedOptions instanceof stdClass) || empty($decodedOptions->dir)) {
            return false;
        }

        if (!is_dir(base_path($decodedOptions->dir))) {
            return false;
        }

        if (empty($decodedOptions->params->bundleNamespace) || empty($decodedOptions->params->entityNamespace)) {
            return false;
        }

        $entityFqns = $decodedOptions->params->bundleNamespace . "\\" . $decodedOptions->params->entityNamespace . "\\";

        foreach (scandir(base_path($decodedOptions->dir)) as $entityFile) {
            if (!file_exists($entityFile) || !is_file($entityFile)) {
                continue;
            }

            $entityName  = str_replace(".php", "", basename($entityFile));
            if (empty($entityName) || $entityName === 'AbstractEntity') {
                continue;
            }

            $entityClass = $entityFqns . $entityName;

            if (!class_exists($entityClass)) {
                continue;
            }

            /** @var AbstractEntity $entity */
            $entity = new $entityClass();
            $constants = $this->generateClassConstantsFromProperties($entity->toArray());
            if ($constants->isEmpty()) {
                continue;
            }

            $fileContents = file_get_contents($entityFile);
            if (empty($fileContents)) {
                continue;
            }

            $tableNameConstant = $this->getTableNameConstantDeclaration($fileContents);

            $constantsDeclaration = $tableNameConstant . "    /** Column name constants */" . PHP_EOL
                . $constants->reduce(function($carry, $constant) {
                return $carry .= $constant;
            });

            $newFileContents = preg_replace(
                "/^class [A-Za-z0-9_\-]+( extends [A-Za-z0-9_\-\\]+)?(\n|\r\n)\{(\n|\r\n)/mi",
                "$0" . $constantsDeclaration . PHP_EOL,
                $fileContents
            );

            if (empty($newFileContents)) {
                continue;
            }

            $result = file_put_contents($entityFile, $newFileContents);
            if (empty($result)) {
                $this->error("Failed to import column and table name class constants for file: $entityFile");
                continue;
            }

            $this->info("Successfully imported column and table name class constants for file: $entityFile");

        }
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

        $properties = new Collection($properties);
        $maxNameLength = $properties->reduce(function ($carry, $propertyName) {
            $nameLength = strlen($propertyName);
            return $nameLength > $carry ? $nameLength : $carry;
        });

        $constants = $properties->map(function ($value, $propertyName) use ($maxNameLength) {
            $nameLengthDiff = $maxNameLength - strlen($propertyName);
            $spacing = '';
            if (!empty($nameLengthDiff)) {
                $spacing = str_repeat(" ", $nameLengthDiff);
            }

            return "    const " . strtoupper($propertyName) . $spacing . " = '$propertyName';" . PHP_EOL;
        });

        return $constants;
    }

    /**
     * Generate a TABLE_NAME class constant for a Doctrine entity class
     *
     * @param string $fileContents
     * @return string
     */
    protected function getTableNameConstantDeclaration(string $fileContents): string
    {
        preg_match("/Table\(name=\"`([A-Za-z0-9\-_]+)`", $fileContents, $matches);
        if (empty($matches)) {
            return '';
        }

        list($wholeMatch, $tableName) = $matches;
        if (empty($tableName)) {
            return '';
        }

        return "    /** Table name constant */" . PHP_EOL . "    const TABLE_NAME = '$tableName';" . PHP_EOL . PHP_EOL;
    }
}
