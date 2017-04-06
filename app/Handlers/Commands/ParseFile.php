<?php

namespace App\Handlers\Commands;

use App\Commands\ParseFile as ParseFileCommand;
use App\Contracts\CustomLogging;
use App\Services\JsonLogService;
use App\Services\Parsers\AbstractXmlParserService;
use App\Services\XmlParserFactoryService;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Monolog\Logger;

class ParseFile extends CommandHandler implements CustomLogging
{
    /** @var JsonLogService */
    protected $logger;

    /**
     * ParseFile constructor.
     *
     * @param JsonLogService $logger
     */
    public function __construct(JsonLogService $logger)
    {
        $this->setLoggerContext($logger);
        $this->logger = $logger;
    }

	/**
	 * Handle the ParseFileCommand
	 *
	 * @param ParseFileCommand $command
	 *
	 * @return bool
	 * @throws FileNotFoundException
	 */
    public function handle(ParseFileCommand $command)
    {
        $file = $command->getFile();

        try {
            // Get the relevant parser service based on the name of the scanner
            /** @var AbstractXmlParserService $service */
            $service = XmlParserFactoryService::getParserService($file->getWorkspaceApp()->getScannerApp()->getName());

            // Process the file
            $service->processXmlFile($file);
        } catch (FileNotFoundException $e) {
            $this->logger->log(Logger::ERROR, 'Could not find file to process', [
                'filePath'    => $file->getPath(),
                'scannerName' => $file->getWorkspaceApp()->getScannerApp()->getName(),
            ]);

            throw $e;
        }

        return true;
    }

    /**
     * @param JsonLogService $logger
     * @return mixed|void
     */
    public function setLoggerContext(JsonLogService $logger)
    {
        $directory = $this->getLogContext();
        $logger->setLoggerName($directory);

        $filename  = $this->getLogFilename();
        $logger->setLogFilename($filename);
    }

    /**
     * @return string
     */
    public function getLogContext(): string
    {
        return 'handler';
    }

    /**
     * @return string
     */
    public function getLogFilename(): string
    {
        return 'parse-file.json.log';
    }
}