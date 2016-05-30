<?php

namespace App\Console\Commands\Schema;

use Illuminate\Console\Command;
use Exception;
use Symfony\Component\Process\Process;
use RuntimeException;


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
    }
}
