<?php

namespace App\Contracts;

use App\Services\JsonLogService;


interface CustomLogging
{
    /**
     * Set the logger instance up for the specific context
     *
     * @param JsonLogHandler $logger
     * @return mixed
     */
    function setLoggerContext(JsonLogService $logger);

    /**
     * The name of the directory to write to/create within storage/logs and use to name the Monolog\Logger instance
     *
     * @return string
     */
    function getLogContext(): string;

    /**
     * The name of the filename to write to within the given directory
     *
     * @return string
     */
    function getLogFilename(): string;
}