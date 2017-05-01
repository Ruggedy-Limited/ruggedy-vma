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
use Symfony\Component\Filesystem\Filesystem;

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

    /** @var Filesystem */
    protected $filesystem;

    /** @var JsonLogService */
    protected $logger;

    /**
     * Create a new command instance.
     *
     * @param FileRepository $fileRepository
     * @param CommandBus $bus
     * @param Filesystem $filesystem
     * @param JsonLogService $logger
     */
    public function __construct(
        FileRepository $fileRepository, CommandBus $bus, FileSystem $filesystem, JsonLogService $logger
    )
    {
        parent::__construct();
        $this->repository = $fileRepository;
        $this->filesystem = $filesystem;
        $this->bus        = $bus;

        $this->setLoggerContext($logger);
        $this->logger = $logger;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Stop and exit early if there is a lock file because this means another cron is already executing.
        if ($this->filesystem->exists($this->getLockFilePath())) {
            $this->warn("Cannot continue processing. Lock file exists.");
            $this->logger->log(Logger::DEBUG, "Cannot continue processing. Lock file exists.", [
                'lockFilePath' => $this->getLockFilePath(),
            ]);

            return;
        }

        $this->filesystem->touch($this->getLockFilePath());

        // Get unprocessed files grouped by workspace ID
        $filesByWorkspace = $this->repository->findUnprocessed();
        if ($filesByWorkspace->isEmpty()) {
            $this->info("No files to process at the moment.");
            $this->filesystem->remove($this->getLockFilePath());
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
                    'fileId'    => $file->getId(),
                    'filePath'  => $file->getPath(),
                    'exception' => $e->getMessage(),
                    'trace'     => $this->logger->getTraceAsArrayOfLines($e),
                ]);

                $this->error("Failed to process file: {$file->getPath()}: {$e->getMessage()}.");
                return;
            }

            $this->info("Successfully processed file: {$file->getPath()}.");
            return;
        });

        $this->filesystem->remove($this->getLockFilePath());
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
     * Get the path to the lock file
     *
     * @return string
     */
    public function getLockFilePath(): string
    {
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'parser.lock';
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
