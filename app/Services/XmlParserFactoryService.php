<?php

namespace App\Services;

use App\Entities\ScannerApp;
use App\Repositories\AssetRepository;
use App\Services\Parsers\AbstractXmlParserService;
use App\Services\Parsers\BurpXmlParserService;
use App\Services\Parsers\NessusXmlParserService;
use App\Services\Parsers\NetsparkerXmlParserService;
use App\Services\Parsers\NexposeXmlParserService;
use App\Services\Parsers\NmapXmlParserService;
use Doctrine\ORM\EntityManager;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use App;
use Illuminate\Validation\Factory;
use App\Repositories\FileRepository;
use League\Tactician\CommandBus;
use XMLReader;

class XmlParserFactoryService
{
    /** @var XMLReader */
    protected static $parser;

    /** @var Filesystem */
    protected static $fileSystem;

    /** @var Factory */
    protected static $validatorFactory;

    /** @var AssetRepository */
    protected static $assetRepository;

    /** @var FileRepository */
    protected static $fileRepository;

    /** @var EntityManager */
    protected static $em;

    /** @var JsonLogService */
    protected static $logger;

    /** @var CommandBus */
    protected static $commandBus;

    /** @var bool */
    protected static $isInitialised = false;

    /** @var Collection */
    protected static $scannerServiceMap;

    /** @var Collection */
    protected static $registeredServices;

    /**
     * Get an instantiated service class for the given scanner name. Returns false if the scanner is not supported or
     * if there is an error instantiating the service
     *
     * @param string $scannerName
     * @return bool
     */
    public static function getParserService(string $scannerName)
    {
        // Initialise the Factory service if it has not already been initialised
        if (!static::$isInitialised) {
            static::initialise();
        }

        // See if the requested service is already registered and if so return it
        $service = static::$registeredServices->get($scannerName, null);
        if ($service instanceof AbstractXmlParserService) {
            return $service;
        }

        // Get the service class name based on the scanner name
        $serviceClassname = static::getServiceClassnameFromScannerName($scannerName);
        if (empty($serviceClassname) || !class_exists($serviceClassname)) {
            return false;
        }

        // Create a new instance of the required service
        $service = new $serviceClassname(
            static::$parser, static::$fileSystem, static::$validatorFactory, static::$assetRepository,
            static::$fileRepository, static::$em, static::$logger, static::$commandBus
        );

        // Register the service instance and then return it
        static::$registeredServices->put($scannerName, $service);

        return $service;
    }

    /**
     * Initialise the factory
     */
    protected static function initialise()
    {
        // Create all the required dependencies and add them to the Factory service
        static::$parser           = App::make(XMLReader::class);
        static::$fileSystem       = App::make(Filesystem::class);
        static::$validatorFactory = App::make(Factory::class);
        static::$assetRepository  = App::make(AssetRepository::class);
        static::$fileRepository   = App::make(FileRepository::class);
        static::$em               = App::make(EntityManager::class);
        static::$logger           = App::make(JsonLogService::class);
        static::$commandBus       = App::make(CommandBus::class);

        // Create a scanner name to service class map
        static::$scannerServiceMap = new Collection([
            ScannerApp::SCANNER_NMAP       => NmapXmlParserService::class,
            ScannerApp::SCANNER_BURP       => BurpXmlParserService::class,
            ScannerApp::SCANNER_NEXPOSE    => NexposeXmlParserService::class,
            ScannerApp::SCANNER_NETSPARKER => NetsparkerXmlParserService::class,
            ScannerApp::SCANNER_NESSUS     => NessusXmlParserService::class,
        ]);

        // Create an empty Collection of registered services and set the $isInitialised flag to true
        static::$registeredServices = new Collection();
        static::$isInitialised      = true;
    }

    /**
     * Check that the scanner is supported and if so return the related service class name
     *
     * @param string $scannerName
     * @return bool|mixed
     */
    public static function getServiceClassnameFromScannerName(string $scannerName)
    {
        $serviceClass = static::$scannerServiceMap->get($scannerName, false);
        if (empty($serviceClass)) {
            return false;
        }

        return $serviceClass;
    }
}