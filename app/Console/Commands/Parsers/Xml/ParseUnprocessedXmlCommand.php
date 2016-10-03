<?php

namespace App\Console\Commands\Parsers\Xml;

use App\Commands\ParseFile;
use App\Contracts\CustomLogging;
use App\Entities\File;
use App\Repositories\FileRepository;
use App\Services\JsonLogService;
use Illuminate\Console\Command;
use League\Tactician\CommandBus;
use Exception;
use Monolog\Logger;

class ParseUnprocessedXmlCommand extends Command implements CustomLogging
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:xml';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse XML scan output. It will parse all uploaded and unprocessed files';
    
    /** @var FileRepository */
    protected $repository;

    /** @var CommandBus */
    protected $bus;

    /** @var JsonLogService */
    protected $logger;

    /**
     * Create a new command instance.
     *
     * @param FileRepository $fileRepository
     * @param CommandBus $bus
     * @param JsonLogService $logger
     */
    public function __construct(FileRepository $fileRepository, CommandBus $bus, JsonLogService $logger)
    {
        parent::__construct();
        $this->repository = $fileRepository;
        $this->bus        = $bus;

        $this->setLoggerContext($logger);
        $this->logger = $logger;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get unprocessed files grouped by workspace ID
        $filesByWorkspace = $this->repository->findUnprocessed();
        if ($filesByWorkspace->isEmpty()) {
            $this->info("No files to process at the moment.");
            return;
        }

        // Iterate over the files
        $filesByWorkspace->each(function ($file) {
            /** @var File $file */
            try {
                $parseFileCommand = new ParseFile($file);
                $this->bus->handle($parseFileCommand);
            } catch (Exception $e) {
                $this->logger->log(Logger::ERROR, "Unhandled exception when parsing file", [
                    'fileInfo' => $file->toArray(),
                ]);

                $this->error("Failed to process file: {$file->getPath()}: {$e->getMessage()}");
                return;
            }

            $this->info("Successfully processing file: {$file->getPath()}.");
            return;
        });
    }

    /**
     * @inheritdoc
     * @param JsonLogService $logger
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
     * @return string
     */
    function getLogContext(): string
    {
        return 'console';
    }

    /**
     * @inheritdoc
     * @return string
     */
    function getLogFilename(): string
    {
        return 'unprocessed-xml-parser.json.log';
    }

    /**
     * @return FileRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @return CommandBus
     */
    public function getBus()
    {
        return $this->bus;
    }

    /**
     * @return JsonLogService
     */
    public function getLogger(): JsonLogService
    {
        return $this->logger;
    }
}
