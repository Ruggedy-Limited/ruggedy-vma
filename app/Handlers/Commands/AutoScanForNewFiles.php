<?php

namespace App\Handlers\Commands;

use App\Commands\AutoScanForNewFiles as AutoScanForNewFilesCommand;
use App\Contracts\CustomLogging;
use App\Entities\File;
use App\Entities\ScannerApp;
use App\Entities\User;
use App\Entities\Workspace;
use App\Entities\WorkspaceApp;
use App\Exceptions\ScannerAppNotFoundException;
use App\Exceptions\UserNotFoundException;
use App\Repositories\FileRepository;
use App\Repositories\ScannerAppRepository;
use App\Repositories\UserRepository;
use App\Repositories\WorkspaceRepository;
use App\Services\JsonLogService;
use App\Services\ScanIdentificationService;
use Doctrine\ORM\EntityManager;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Filesystem\Filesystem;
use Monolog\Logger;
use ProxyManager\Exception\FileNotWritableException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class AutoScanForNewFiles extends CommandHandler implements CustomLogging
{
    /** @var Filesystem */
    protected $fileSystem;

    /** @var FileRepository */
    protected $fileRepository;

    /** @var WorkspaceRepository */
    protected $workspaceRepository;

    /** @var UserRepository */
    protected $userRepository;

    /** @var ScannerAppRepository */
    protected $scannerAppRepository;

    /** @var EntityManager */
    protected $em;

    /** @var ScanIdentificationService */
    protected $service;

    /** @var JsonLogService */
    protected $logger;

    /** @var Workspace */
    protected $workspace;

    /** @var Collection */
    protected $workspaceApps;

    /**
     * AutoScanForNewFiles constructor.
     *
     * @param Filesystem $filesystem
     * @param FileRepository $fileRepository
     * @param WorkspaceRepository $workspaceRepository
     * @param UserRepository $userRepository
     * @param ScannerAppRepository $scannerAppRepository
     * @param EntityManager $entityManager
     * @param ScanIdentificationService $service
     * @param JsonLogService $logger
     */
    public function __construct(
        Filesystem $filesystem, FileRepository $fileRepository, WorkspaceRepository $workspaceRepository,
        UserRepository $userRepository, ScannerAppRepository $scannerAppRepository, EntityManager $entityManager,
        ScanIdentificationService $service, JsonLogService $logger)
    {
        $this->fileSystem           = $filesystem;
        $this->fileRepository       = $fileRepository;
        $this->workspaceRepository  = $workspaceRepository;
        $this->userRepository       = $userRepository;
        $this->scannerAppRepository = $scannerAppRepository;
        $this->em                   = $entityManager;
        $this->service              = $service;
        $this->workspaceApps        = collect();

        $this->setLoggerContext($logger);
        $this->logger = $logger;
    }

    /**
     * Process the AutoScanForNewFiles command.
     *
     * @param AutoScanForNewFilesCommand $command
     * @return Collection
     */
    public function handle(AutoScanForNewFilesCommand $command): Collection
    {
        // autoScan must be set
        if (empty($command->isAutoScan())) {
            return collect();
        }

        // Get the files in the auto_scan directory and return an empty collection if there are none
        $files = collect($this->fileSystem->files($this->getAutoScanPath()));
        if ($files->isEmpty()) {
            return collect();
        }

        // Return a mapped collection of true/false. True = file processed successfully, false = error
        return $files->map(function($filepath) {
            // Basic sanitisation of file name
            $filepath = $this->sanitiseFilename($filepath);

            // Create an UploadedFile instance
            $uploadedFile = new UploadedFile(
                $filepath,
                basename($filepath),
                mime_content_type($filepath),
                filesize($filepath),
                UPLOAD_ERR_OK,
                true
            );

            // Create a File entity instance
            $file = new File();
            $file->setName(
                $this->convertRawFilenameToReadableName(basename($filepath))
            );

            // Try and process each file and catch exceptions and log an error but just return false to report the
            // number of failures to the console
            try {
                $this->processFile($uploadedFile, $file);
            } catch (Exception $e) {
                $this->logger->log(Logger::ERROR, "Could not process file found in auto_scan directory", [
                    'filepath'  => $filepath,
                    'exception' => $e->getMessage(),
                    'trace'     => $this->logger->getTraceAsArrayOfLines($e),
                ]);

                // Being extra defensive here because we're doing this in a catch block and unlink will raise an
                // E_WARNING level error on failure: @see http://php.net/manual/en/function.unlink.php
                if (file_exists($filepath)) {
                    unlink($filepath);
                }

                return false;
            }

            return true;
        });
    }

    /**
     * Process a file picked up in the auto scan directory
     *
     * @param UploadedFile $uploadedFile
     * @param File $file
     * @return File
     */
    protected function processFile(UploadedFile $uploadedFile, File $file)
    {
        $workspaceApp = $this->getWorkspaceApp($uploadedFile);

        // Store the file
        if (!$this->service->storeUploadedFile($workspaceApp->getId())) {
            throw new FileNotWritableException("Could not store uploaded file on server");
        }

        // Set the rest of the details on the File entity, persist it to the DB and return it
        $file->setPath(
            $this->service->getProvisionalStoragePath($workspaceApp->getId()) . $uploadedFile->getClientOriginalName()
        );
        $file->setFormat($this->service->getFormat());
        $file->setSize($uploadedFile->getClientSize());
        $file->setUser($workspaceApp->getWorkspace()->getUser());
        $file->setWorkspaceApp($workspaceApp);
        $file->setDeleted(false);
        $file->setProcessed(false);

        $this->em->persist($file);
        $this->em->flush($file);

        return $file;
    }

    /**
     * Get the WorkspaceApp for the file
     *
     * @param UploadedFile $uploadedFile
     * @return WorkspaceApp
     */
    protected function getWorkspaceApp(UploadedFile $uploadedFile): WorkspaceApp
    {
        // Get the auto scan workspace
        $workspace = $this->getWorkspace();

        // Initialise the scanner identification service and make sure the file is recognisable
        if (!$this->service->initialise($uploadedFile)) {
            throw new FileException("Could not match the file to any supported scanner output");
        }

        // Check for a cached version of the WorkspaceApp in the handler
        $scanner = $this->service->getScanner();
        if (!empty($this->workspaceApps->get($scanner))) {
            return $this->workspaceApps->get($scanner);
        }

        // Look for an existing workspace app in the auto scan workspace
        $workspaceApp = $workspace->getWorkspaceApps()->filter(function ($workspaceApp) use ($scanner) {
            /** @var WorkspaceApp $workspaceApp */
            return $workspaceApp->getScannerApp()->getName() === $scanner;
        })->first();

        // If there is no existing Workspace App for this scanner, create one in the auto scan workspace
        if (empty($workspaceApp)) {
            $workspaceApp = $this->createAutoScanWorkspaceApp($workspace, $scanner);
        }

        // Cache the workspace app in the handler and return it
        $this->workspaceApps->put($scanner, $workspaceApp);
        return $workspaceApp;
    }

    /**
     * Get the auto scan workspace
     *
     * @return Workspace
     */
    protected function getWorkspace(): Workspace
    {
        // If the workspace is cached in handler return the cached instance
        if (!empty($this->workspace)) {
            return $this->workspace;
        }

        // Try to find the existing auto scan workspace and if it doesn't exist, create it
        /** @var Workspace $workspace */
        $workspace = $this->workspaceRepository->findOneBy([Workspace::NAME => Workspace::AUTO_SCAN_WORKSPACE_NAME]);
        if (empty($workspace)) {
            $workspace = $this->createAutoScanWorkspace();
        }

        // Cache the workspace in the handler and return it
        $this->workspace = $workspace;
        return $workspace;
    }

    /**
     * Create a workspace for workspace apps and files of a certain scanner app that were picked up by the auto scanner
     *
     * @return Workspace
     * @throws UserNotFoundException
     */
    protected function createAutoScanWorkspace(): Workspace
    {
        // Find the first admin user
        $user = $this->userRepository->findOneBy([User::IS_ADMIN => true]);
        if (empty($user)) {
            throw new UserNotFoundException("No admin User was found to set as the owner for the auto scan Workspace.");
        }

        // Create a new workspace with auto scan name and description
        $workspace = new Workspace();
        $workspace->setName(Workspace::AUTO_SCAN_WORKSPACE_NAME)
            ->setDescription(Workspace::AUTO_SCAN_WORKSPACE_DESCRIPTION)
            ->setDeleted(false)
            ->setUser($user);

        $this->em->persist($workspace);
        $this->em->flush($workspace);
        $this->em->refresh($workspace);

        return $workspace;
    }

    /**
     * Create a workspace app for files of a certain scanner app that were picked up by the auto scanner
     *
     * @param Workspace $workspace
     * @param string $scanner
     * @return WorkspaceApp
     * @throws ScannerAppNotFoundException
     */
    protected function createAutoScanWorkspaceApp(Workspace $workspace, string $scanner): WorkspaceApp
    {
        // Find the relevant scanner app
        $scannerApp = $this->scannerAppRepository->findOneBy([ScannerApp::NAME => $scanner]);
        if (empty($scannerApp)) {
            throw new ScannerAppNotFoundException("No scanner app found for the given file.");
        }

        // Create the new WorkspaceApp
        $workspaceApp = new WorkspaceApp();
        $workspaceApp->setName(WorkspaceApp::AUTO_SCAN_WORKSPACE_APP_NAME)
            ->setDescription(WorkspaceApp::AUTO_SCAN_WORKSPACE_APP_DESCRIPTION)
            ->setWorkspace($workspace)
            ->setScannerApp($scannerApp);
        
        $this->em->persist($workspaceApp);
        $this->em->flush($workspaceApp);
        $this->em->refresh($workspaceApp);
        
        return $workspaceApp;
    }

    /**
     * Sanitise file names of files picked up automatically to be more secure. A file named maliciously and not
     * sanitised could create a SQL injection or other vulnerabilities
     *
     * @param string $filepath
     * @return string
     */
    protected function sanitiseFilename(string $filepath): string
    {
        // Basic Sanitisation of file name
        $filename = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', basename($filepath));
        if ($filename === basename($filepath)) {
            return $filepath;
        }

        // If the name needed to be sanitised, make a copy with the sanitised name and delete the original
        $sanitisedPath = dirname($filepath) . DIRECTORY_SEPARATOR . $filename;
        copy($filepath, $sanitisedPath);
        unlink($filepath);

        return $sanitisedPath;
    }

    /**
     * Convert the file name into a name to be stored in the DB
     *
     * @param string $filename
     * @return string
     */
    protected function convertRawFilenameToReadableName(string $filename)
    {
        // Strip the extension if there is one
        if (strpos($filename, '.') !== false) {
            $filename = substr($filename, 0, strrpos($filename, '.'));
        }

        return ucfirst(str_replace(['_', '-', '.'], ' ', $filename));
    }

    /**
     * Get the path to search for files that should be added automagically
     *
     * @return string
     */
    protected function getAutoScanPath(): string
    {
        return $this->service->getScanFileStorageBasePath() . 'auto_scan';
    }

    /**
     * @inheritdoc
     * @param JsonLogService $logger
     */
    public function setLoggerContext(JsonLogService $logger)
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
    public function getLogContext(): string
    {
        return 'handler';
    }

    /**
     * @inheritdoc
     * @return string
     */
    public function getLogFilename(): string
    {
        return 'autoscan.json.log';
    }
}