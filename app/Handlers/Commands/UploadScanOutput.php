<?php

namespace App\Handlers\Commands;

use App\Commands\UploadScanOutput as UploadScanOutputCommand;
use App\Entities\File;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\InvalidInputException;
use App\Exceptions\ScannerAppNotFoundException;
use App\Exceptions\WorkspaceNotFoundException;
use App\Policies\ComponentPolicy;
use App\Repositories\ScannerAppRepository;
use App\Repositories\WorkspaceRepository;
use App\Services\ScanIdentificationService;
use Doctrine\ORM\EntityManager;
use Exception;
use ProxyManager\Exception\FileNotWritableException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class UploadScanOutput extends CommandHandler
{
    const MIME_TYPE_XML = 'application/xml';

    /** @var WorkspaceRepository */
    protected $workspaceRepository;

    /** @var ScannerAppRepository */
    protected $scannerAppRepository;

    /** @var ScanIdentificationService */
    protected $service;

    /** @var EntityManager */
    protected $em;

    /**
     * UploadScanOutput constructor.
     *
     * @param WorkspaceRepository $workspaceRepository
     * @param ScannerAppRepository $scannerAppRepository
     * @param EntityManager $em
     * @param ScanIdentificationService $service
     * @internal param Filesystem $fileSystem
     */
    public function __construct(
        WorkspaceRepository $workspaceRepository, ScannerAppRepository $scannerAppRepository,
        EntityManager $em, ScanIdentificationService $service
    )
    {
        $this->workspaceRepository  = $workspaceRepository;
        $this->scannerAppRepository = $scannerAppRepository;
        $this->service              = $service;
        $this->em                   = $em;
    }

    /**
     * @param UploadScanOutputCommand $command
     * @return File
     * @throws ActionNotPermittedException
     * @throws InvalidInputException
     * @throws WorkspaceNotFoundException
     * @throws Exception
     */
    public function handle(UploadScanOutputCommand $command)
    {
        // Get the authenticated User
        $requestingUser = $this->authenticate();

        // Check that all the required members are set on the command
        $workspaceId = $command->getId();
        $file        = $command->getFile();

        if (!isset($workspaceId, $file)) {
            throw new InvalidInputException("One or more required members are not set on the command");
        }

        // Check that the given Workspace exists
        $workspace = $this->getWorkspaceRepository()->find($workspaceId);
        if (empty($workspace)) {
            throw new WorkspaceNotFoundException("There was no existing Workspace with the given ID");
        }

        // Check the authenticated User's permission
        if ($requestingUser->cannot(ComponentPolicy::ACTION_CREATE, $workspace)) {
            throw new ActionNotPermittedException(
                "The authenticated User does not have permission to create new Assets on the given Workspace"
            );
        }

        $scanner =  $this->getService()->initialise($file);
        if (!$scanner) {
            throw new FileException("Could not match the file to any supported scanner output");
        }

        $scannerApp = $this->getScannerAppRepository()->findByName($scanner);
        if (empty($scannerApp)) {
            throw new ScannerAppNotFoundException("No scanner app with the given name was found");
        }

        if (!$this->getService()->storeUploadedFile($workspaceId)) {
            throw new FileNotWritableException("Could not store uploaded file on server");
        }

        // Create a new File entity, persist it to the DB and return it
        $fileEntity = new File();
        $fileEntity->setPath(
            $this->getService()->getProvisionalStoragePath($workspaceId) . $file->getClientOriginalName()
        );
        $fileEntity->setFormat($this->getService()->getFormat());
        $fileEntity->setSize($file->getClientSize());
        $fileEntity->setUser($requestingUser);
        $fileEntity->setWorkspace($workspace);
        $fileEntity->setScannerApp($scannerApp);
        $fileEntity->setDeleted(false);
        $fileEntity->setProcessed(false);

        $this->getEm()->persist($fileEntity);
        $this->getEm()->flush($fileEntity);

        return $fileEntity;
    }

    /**
     * Get the base storage path for scanner files
     *
     * @return string
     */
    protected function getScanFileStorageBasePath(): string
    {
        return storage_path('scans') . DIRECTORY_SEPARATOR;
    }

    /**
     * @return WorkspaceRepository
     */
    public function getWorkspaceRepository()
    {
        return $this->workspaceRepository;
    }

    /**
     * @return ScannerAppRepository
     */
    public function getScannerAppRepository(): ScannerAppRepository
    {
        return $this->scannerAppRepository;
    }

    /**
     * @return ScanIdentificationService
     */
    public function getService(): ScanIdentificationService
    {
        return $this->service;
    }

    /**
     * @return EntityManager
     */
    public function getEm()
    {
        return $this->em;
    }
}