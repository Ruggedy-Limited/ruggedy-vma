<?php

namespace App\Handlers\Commands;

use App\Commands\ParseFile as ParseFileCommand;
use App\Services\JsonLogService;
use App\Services\Parsers\AbstractXmlParserService;
use App\Services\XmlParserFactoryService;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Monolog\Logger;

class ParseFile
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
        $this->logger = $logger;
    }

    /**
     * Handle the ParseFileCommand
     *
     * @param ParseFileCommand $command
     * @return bool
     */
    public function handle(ParseFileCommand $command)
    {
        $file = $command->getFile();

        try {
            // Get the relevant parser service based on the name of the scanner
            /** @var AbstractXmlParserService $service */
            $service = XmlParserFactoryService::getParserService($file->getScannerApp()->getName());

            // Process the file
            $service->processXmlFile($file);
        } catch (FileNotFoundException $e) {
            $this->logger->log(Logger::ERROR, 'Could not find file to process', [
                'filePath'    => $file->getPath(),
                'scannerName' => $file->getScannerApp()->getName(),
            ]);

            return false;
        }

        return true;
    }
}