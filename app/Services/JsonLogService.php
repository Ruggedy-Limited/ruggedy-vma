<?php

namespace App\Services;

use Monolog\Formatter\JsonFormatter;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Exception;

class JsonLogService
{
    /** @var  Logger */
    protected $logger;

    /** @var  string */
    protected $callingClass;

    /** @var  string */
    protected $callingMethod;

    /** @var  string */
    protected $description;

    /** @var  string */
    protected $logFilename;

    /** @var  string */
    protected $loggerName;

    /**
     * Initialise the logger for the current context
     */
    public function initLogger()
    {
        if (empty($this->loggerName)) {
            throw new Exception("Cannot create a logger without a logger name.");
        }

        $logger = new Logger($this->loggerName);
        $this->setLogger($logger);

        $logDirectory = storage_path('logs') . DIRECTORY_SEPARATOR . $this->loggerName;
        if (!realpath($logDirectory)) {
            mkdir($logDirectory, 0755, true);
        }

        if (!realpath($logDirectory)) {
            throw new Exception("Could not create the directory [$logDirectory] to log to.");
        }

        $handlers = $this->getStreamHandlers($logDirectory);
        $this->logger->setHandlers($handlers);
    }

    /**
     * Get the log stream handlers
     *
     * @param $logDirectory
     * @return array
     */
    protected function getStreamHandlers($logDirectory)
    {
        $streamHandler = new StreamHandler($logDirectory . DIRECTORY_SEPARATOR . $this->logFilename);
        $streamHandler->pushProcessor([$this, 'streamProcessor']);
        $streamHandler->setFormatter(new JsonFormatter());

        return [$streamHandler];
    }

    /**
     * Process the stream when passed to the logger
     *
     * @param $record
     * @return mixed
     */
    public function streamProcessor ($record) {
        if (empty($record)) {
            return $record;
        }

        if (!isset($record['extra'])) {
            $record['extra'] = [];
        }

        $extra                =& $record['extra'];
        $extra['class']       = $this->callingClass;
        $extra['method']      = $this->callingMethod;
        $extra['description'] = $this->description;
        $extra['env']         = env('APP_ENV');

        return $record;
    }

    /**
     * Log something
     *
     * @param int $level
     * @param null $message
     * @param array $context
     * @param null $description
     */
    public function log($level = Logger::INFO, $message = null, $context = [], $description = null)
    {
        $this->initLogger();

        $this->setDescription($description);

        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        $callingClass = $backtrace[1]['class'] ?: null;
        $this->setCallingClass($callingClass);

        $callingMethod = $backtrace[1]['function'] ?: null;
        $this->setCallingMethod($callingMethod);

        if (!is_array($context)) {
            $context = [$context];
        }

        try {
            $this->logger->log($level, $message, $context);
        } catch (\Exception $e) {
            // TODO: Setup a Nagios alert to monitor long periods of inactivity in the log files
        }
    }

    /**
     * Split the string exception trace into an array of lines
     *
     * @param Exception $exception
     * @return string
     */
    public function getTraceAsArrayOfLines(Exception $exception)
    {
        if (empty($exception->getTraceAsString()) || strpos($exception->getTraceAsString(), PHP_EOL) === false) {
            return $exception->getTraceAsString();
        }

        return explode(PHP_EOL, $exception->getTraceAsString());
    }

    /**
     * @return Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param Logger $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return string
     */
    public function getCallingClass()
    {
        return $this->callingClass;
    }

    /**
     * @param string $callingClass
     */
    public function setCallingClass($callingClass)
    {
        $this->callingClass = $callingClass;
    }

    /**
     * @return string
     */
    public function getCallingMethod()
    {
        return $this->callingMethod;
    }

    /**
     * @param string $callingMethod
     */
    public function setCallingMethod($callingMethod)
    {
        $this->callingMethod = $callingMethod;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getLogFilename()
    {
        return $this->logFilename;
    }

    /**
     * @param string $logFilename
     */
    public function setLogFilename($logFilename)
    {
        $this->logFilename = $logFilename;
    }

    /**
     * @return string
     */
    public function getLoggerName()
    {
        return $this->loggerName;
    }

    /**
     * @param string $loggerName
     */
    public function setLoggerName($loggerName)
    {
        $this->loggerName = $loggerName;
    }
}