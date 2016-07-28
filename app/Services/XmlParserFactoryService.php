<?php

namespace App\Services;

use App\Services\Parsers\AbstractXmlParserService;
use App\Services\Parsers\BurpXmlParserService;
use App\Services\Parsers\NmapXmlParserService;
use Doctrine\ORM\EntityManager;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\Factory;
use App\Repositories\FileRepository;
use Symfony\Component\Filesystem\Filesystem;
use XMLReader;

class XmlParserFactoryService
{
    const SCANNER_NMAP = 'nmap';
    const SCANNER_BURP = 'burp';

    /** @var XMLReader */
    protected static $parser;

    /** @var Filesystem */
    protected static $fileSystem;

    /** @var Factory */
    protected static $validatorFactory;

    /** @var FileRepository */
    protected static $fileRepository;

    /** @var EntityManager */
    protected static $em;

    /** @var JsonLogService */
    protected static $logger;

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
        if (!static::$isInitialised) {
            static::initialise();
        }

        $service = static::$registeredServices->get($scannerName, null);
        if ($service instanceof AbstractXmlParserService) {
            return $service;
        }

        $serviceClassname = static::getServiceClassnameFromScannerName($scannerName);
        if (empty($serviceClassname) || !class_exists($serviceClassname)) {
            return false;
        }

        $service = new $serviceClassname(
            static::$parser, static::$fileSystem, static::$validatorFactory,
            static::$fileRepository, static::$em, static::$logger
        );

        static::$registeredServices->put($scannerName, $service);

        return $service;
    }

    /**
     * Initialise the factory
     */
    protected static function initialise()
    {
        static::$parser           = App::make(XMLReader::class);
        static::$fileSystem       = App::make(Filesystem::class);
        static::$validatorFactory = App::make(Factory::class);
        static::$fileRepository   = App::make(FileRepository::class);
        static::$em               = App::make(EntityManager::class);
        static::$logger           = App::make(JsonLogService::class);

        static::$scannerServiceMap = new Collection([
            self::SCANNER_NMAP => NmapXmlParserService::class,
            self::SCANNER_BURP => BurpXmlParserService::class,
        ]);

        static::$registeredServices = new Collection();
        static::$isInitialised = true;
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