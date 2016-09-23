<?php

namespace App\Console\Commands\Parsers\Xml;

use App\Commands\CommitCurrentUnitOfWork;
use App\Commands\CreateAsset;
use App\Commands\CreateSoftwareInformation;
use App\Commands\CreateVulnerability;
use App\Commands\CreateVulnerabilityReference;
use App\Commands\CreateOpenPort;
use App\Commands\ParseFile;
use App\Contracts\CollectsScanOutput;
use App\Contracts\CustomLogging;
use App\Contracts\SystemComponent;
use App\Entities\Asset;
use App\Entities\Base\AbstractEntity;
use App\Entities\File;
use App\Entities\OpenPort;
use App\Entities\SoftwareInformation;
use App\Entities\User;
use App\Entities\Vulnerability;
use App\Entities\VulnerabilityReferenceCode;
use App\Repositories\FileRepository;
use App\Services\JsonLogService;
use App\Services\Parsers\AbstractXmlParserService;
use App\Services\XmlParserFactoryService;
use Doctrine\ORM\ORMException;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
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
     *
     * @return mixed
     */
    public function handle()
    {
        // Get unprocessed files grouped by workspace ID
        $filesByWorkspace = $this->repository->findUnprocessed();
        if ($filesByWorkspace->isEmpty()) {
            $this->info("No files to process at the moment.");
            return true;
        }

        // Iterate over the files
        $filesByWorkspace->each(function ($file, $offset) {
            /** @var File $file */
            try {
                $parseFileCommand = new ParseFile($file);
                $this->bus->handle($parseFileCommand);
            } catch (Exception $e) {
                $this->logger->log(Logger::ERROR, "Unhandled exception when parsing file", [
                    'fileInfo' => $file->toArray(),
                ]);

                $this->error("Failed to process file: {$file->getPath()}: {$e->getMessage()}");
                return true;
            }

            $this->info("Successfully processing file: {$file->getPath()}.");
            return true;
        });
    }

    /**
     * Process a single NMAP file
     *
     * @param File $file
     * @return bool
     */
    protected function processFile(File $file)
    {
        try {
            // Get the relevant parser service based on the name of the scanner
            /** @var AbstractXmlParserService $service */
            $service = XmlParserFactoryService::getParserService($file->getScannerApp()->getName());

            // Process the file and extract a Collection of XML models
            /** @var Collection $xmlParserModels */
            $xmlParserModels = $service->processXmlFile($file);
        } catch (FileNotFoundException $e) {
            $this->logger->log(Logger::ERROR, 'Could not find file to process', [
                'filePath'    => $file->getPath(),
                'scannerName' => $file->getScannerApp()->getName(),
            ]);
            
            $this->error("Error processing file: $file: {$e->getMessage()}");

            return true;
        }

        // If there were no models generated show a warning message
        if ($xmlParserModels->isEmpty()) {
            $this->logger->log(Logger::WARNING, 'No models generated from parsing file', [
                'filePath' => $file->getPath(),
                'scannerName' => $file->getScannerApp()->getName(),
            ]);

            $this->warn("No models generated by file: $file.");
            return true;
        }

        // Intialise some counters
        $counters = new Collection([
            Asset::class                      => new Collection(),
            Vulnerability::class              => new Collection(),
            VulnerabilityReferenceCode::class => new Collection(),
            OpenPort::class                   => new Collection(),
        ]);

        // Get the Workspace ID from the file entity
        $workspaceId = $file->getWorkspaceId();

        // Authenticate the User who uploaded the file so that their permissions will be used for applying any
        // operations that result from processing the file
        $currentUser = Auth::user();
        $fileUser    = $file->getUser();
        if (empty($currentUser) || !($currentUser instanceof User)
            || $currentUser->getId() !== $fileUser->getId()) {

            Auth::login($fileUser);
            
        }

        // Iterate over each model, extract and persist the relevant entities
        $xmlParserModels->each(function($model, $offset) use ($workspaceId, $file, $counters) {
            $this->processXmlParserModel($model, $workspaceId, $counters, $file);
            return true;
        });

        // Move the file to the processed location
        $service->moveFileToProcessed($file);

        // No Assets successfully created :( reset the service models and return
        if ($counters->get(Asset::class)->count() === 0) {
            $this->logger->log(Logger::WARNING, "No Assets generated from file", [
                'filePath'    => $file->getPath(),
                'scannerName' => $file->getScannerApp()->getName(),
                'noOfModels'  => $xmlParserModels->count(),
            ]);
            $this->warn("Did not successfully create any Assets from file: {$file->getPath()}");
            $service->resetModels();
            return true;
        }

        // Generate output for the console
        $this->generateConsoleMessages($file, $counters);

        // Reset the collection of Models to an empty Collection in preparation from processing the next file.
        $service->resetModels();

        return true;
    }

    /**
     * Process the XML Parser Model
     *
     * @param CollectsScanOutput $model
     * @param int $workspaceId
     * @param Collection $counters
     * @param File $file
     * @return bool
     */
    protected function processXmlParserModel(
        CollectsScanOutput $model, int $workspaceId, Collection $counters, File $file
    ): bool
    {
        // Instantiate a CreateAsset command and attempt to execute the command
        /** @var CollectsScanOutput $model */
        $assetDetails = $model->exportForAsset();

        // Get vulnerability, vulnerability reference and system information
        $vulnerabilityDetails    = $model->exportForVulnerability();
        $vulnerabilityRefDetails = $model->exportForVulnerabilityReference();
        $openPortDetails         = $model->exportOpenPorts();
        $softwareInformation     = $model->exportSoftwareInformation();

        // Save/update the Asset
        $asset = $this->sendCommandToBus(CreateAsset::class, $workspaceId, $assetDetails);

        // Make sure we got an Asset entity back from the command. If not, we can't continue, because Open Ports
        // and Vulnerabilities need to be related to an Asset entity
        if (empty($asset) || !($asset instanceof Asset)) {
            return true;
        }

        // Add the asset ID to the collection
        $counters->get(Asset::class)->put($asset->getId(), true);

        // Save the open ports for this Asset
        $this->prepareDetailsForCommandAndSend(
            OpenPort::class, CreateOpenPort::class, $openPortDetails, $asset, $counters, $file
        );

        // Save the software information for this Asset
        $this->prepareDetailsForCommandAndSend(
            SoftwareInformation::class, CreateSoftwareInformation::class, $softwareInformation, $asset, $counters
        );

        // Call the CreateVulnerability command
        $vulnerability = $this->sendCommandToBus(
            CreateVulnerability::class, $asset->getId(), $vulnerabilityDetails, $file
        );

        // If we did not get a Vulnerability Entity back from the command we cannot continue because
        // VulnerabilityReferenceCodes must have a related Vulnerability
        if (empty($vulnerability) || !($vulnerability instanceof Vulnerability)) {
            return true;
        }

        // Increment the Vulnerability count
        $counters->get(Vulnerability::class)->put($vulnerability->getId(), true);

        $this->prepareDetailsForCommandAndSend(
            VulnerabilityReferenceCode::class, CreateVulnerabilityReference::class, $vulnerabilityRefDetails,
            $vulnerability, $counters
        );

        /* If there is associated vulnerability reference data in the model, save a new vulnerability reference
        $vulnerabilityReference = $this->sendCommandToBus(
            CreateVulnerabilityReference::class, $vulnerability->getId(), $vulnerabilityRefDetails
        );

        // Increment the VulnerabilityReferenceCode failed counter if we did not get a VulnerabilityReferenceCode
        // entity from the CreateVulnerabilityReference command
        if (empty($vulnerabilityReference)) {
            return true;
        }

        // Increment the VulnerabilityReferenceCode counter if we got a VulnerabilityReferenceCode entity from the
        // CreateVulnerabilityReference command
        $counters->get(VulnerabilityReferenceCode::class)->put($vulnerabilityReference->getId(), true);*/

        return true;
    }

    /**
     * Prepare the Collection of models and pass to the command bus
     *
     * @param string $entityClass
     * @param string $commandClass
     * @param Collection $details
     * @param SystemComponent $relatedEntity
     * @param Collection $counters
     * @param File|null $file
     * @param bool $multiMode
     * @return bool|SystemComponent
     */
    protected function prepareDetailsForCommandAndSend(
        string $entityClass, string $commandClass, Collection $details, SystemComponent $relatedEntity,
        Collection $counters, File $file = null, $multiMode = false
    )
    {
        if (empty($entityClass) || empty($commandClass) || empty($relatedEntity)) {
            $this->logger->log(Logger::ERROR, "One or more required parameters are empty", [
                'requiredParameters' => ['entityClass', 'commandClass', 'relatedEntity', 'details'],
                'entityClass'        => $entityClass ?? null,
                'commandClass'       => $commandClass ?? null,
                'relatedEntity'      => $relatedEntity instanceof AbstractEntity ? $relatedEntity->toArray() : null,
                'details'            => $details->toArray(),
            ]);

            return false;
        }

        if ($details->isEmpty()) {
            $this->logger->log(Logger::NOTICE, "No details received to send with command", [
                'entityClass'  => $entityClass ?? null,
                'commandClass' => $commandClass ?? null,
            ]);

            return false;
        }

        $lastEntity = $details->reduce(function($carry, $detailsForEntity) use (
            $commandClass, $entityClass, $relatedEntity, $file, $relatedEntity, $counters, $multiMode
        ) {
            $entityResult = $this->sendCommandToBus(
                $commandClass, $relatedEntity->getId(), $detailsForEntity, $file, $multiMode
            );

            if (empty($entityResult) || !($entityResult instanceof SystemComponent)) {
                return null;
            }

            // If we got an instance of a SystemComponent contract from the command increment the counter
            $counters->get($entityClass)->put($entityResult->getId(), true);

            return $entityResult;
        });

        return $lastEntity;
    }

    /**
     * Instantiate the relevant command and send it over the command bus
     *
     * @param string $commandClass
     * @param int $id
     * @param Collection $details
     * @param File $file
     * @param bool $multiMode
     * @return SystemComponent|false
     */
    public function sendCommandToBus(
        string $commandClass, int $id, Collection $details, File $file = null, bool $multiMode = false
    )
    {
        // Make sure we got a valid command class
        if (!class_exists($commandClass)) {
            $this->logger->log(Logger::ERROR, "Command class does not exist", [
                'commandClass'  => $commandClass ?? null,
                'id'            => $id ?? null,
                'entityDetails' => $details->toJson(),
                'filePath'      => isset($file) ? $file->getPath() : null,
            ]);
            $this->error("Could not find command class: $commandClass.");

            return false;
        }

        // Make sure we got the necessary foreign key and details for the command
        if (!isset($id) || $details->isEmpty()) {
            $this->logger->log(Logger::WARNING, "Foreign key or entity details missing", [
                'commandClass'  => $commandClass ?? null,
                'id'            => $id ?? null,
                'entityDetails' => $details->toJson(),
                'filePath'      => isset($file) ? $file->getPath() : null,
            ]);
            return false;
        }

        // Add the file to the details where applicable
        if (!empty($file)) {
            $details->put('file', $file);
        }

        // Try to execute the command and catch any exceptions
        try {
            $command = new $commandClass($id, $details->toArray(), $multiMode);
            return $this->bus->handle($command);
        } catch (Exception $e) {
            $this->logger->log(Logger::ERROR, "Exception occurred when executing command", [
                'commandClass'     => $commandClass ?? null,
                'id'               => $id ?? null,
                'entityDetails'    => $details->toJson(),
                'multimode'        => $multiMode,
                'filePath'         => isset($file) ? $file->getPath() : null,
                'exceptionMessage' => $e->getMessage(),
                'exceptionTrace'   => $e->getTraceAsString(),
            ]);

            $this->error("Error: {$e->getMessage()} when handling command: $commandClass.");

            return false;
        }
    }

    /**
     * Generate final console messages related to the most recently parsed file
     *
     * @param File $file
     * @param Collection $counters
     */
    protected function generateConsoleMessages(File $file, Collection $counters)
    {
        $this->info("File: {$file->getPath()}");
        $counters->each(function ($counter, $class) {
            return $this->generateEntityRelatedConsoleMessages($counter, $class);
        });
        $this->info(PHP_EOL);
    }

    /**
     * Generate console messages for each type of Entity that was created for most recently processed file
     *
     * @param $counter
     * @param $class
     * @return bool
     */
    protected function generateEntityRelatedConsoleMessages($counter, $class)
    {
        // Defensiveness
        if (empty($class) || empty($counter) || !($counter instanceof Collection)) {
            return true;
        }

        // We only need to generate messages where there were entities created
        if (!($counter->count() > 0)) {
            return true;
        }

        // Split the class name into parts and make sure we got a valid result
        $classParts = explode("\\", $class);
        if (empty($classParts) || !is_array($classParts)) {
            return true;
        }

        // Convert the entity name to the plural version and make sure we got a valid result
        $entityName          = array_pop($classParts);
        $entityPluralisation = $this->pluraliseEntityName($entityName);
        if (empty($entityPluralisation)) {
            return true;
        }

        // Output a counter message for the current entity type
        $this->info("Successfully created/updated {$counter->count()} $entityPluralisation");

        return true;
    }

    /**
     * Get the plural form of the Entity's name
     *
     * @param string $entityName
     * @return null|string
     */
    protected function pluraliseEntityName(string $entityName)
    {
        // Make sure we received something
        if (empty($entityName)) {
            return null;
        }

        $lastChar            = substr($entityName, -1);
        $entityPluralisation = $entityName . "s";

        // Check if the last character is 'y' and if so, pluralise with 'ies' instead of just 's'
        if ($lastChar === 'y') {
            $entityPluralisation = rtrim($entityName, "y") . "ies";
        }

        return $entityPluralisation;
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
