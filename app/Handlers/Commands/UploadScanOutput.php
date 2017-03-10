<?php

namespace App\Handlers\Commands;

use App\Commands\UploadScanOutput as UploadScanOutputCommand;
use App\Entities\File;
use App\Entities\WorkspaceApp;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\InvalidInputException;
use App\Exceptions\WorkspaceAppNotFoundException;
use App\Exceptions\WorkspaceNotFoundException;
use App\Policies\ComponentPolicy;
use App\Repositories\ScannerAppRepository;
use App\Repositories\WorkspaceAppRepository;
use App\Services\ScanIdentificationService;
use Doctrine\ORM\EntityManager;
use Exception;
use ProxyManager\Exception\FileNotWritableException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class UploadScanOutput extends CommandHandler
{
    const MIME_TYPE_XML = 'application/xml';

    /** @var WorkspaceAppRepository */
    protected $workspaceAppRepository;

    /** @var ScannerAppRepository */
    protected $scannerAppRepository;

    /** @var ScanIdentificationService */
    protected $service;

    /** @var EntityManager */
    protected $em;

    /**
     * UploadScanOutput constructor.
     *
     * @param WorkspaceAppRepository $workspaceAppRepository
     * @param ScannerAppRepository $scannerAppRepository
     * @param EntityManager $em
     * @param ScanIdentificationService $service
     * @internal param Filesystem $fileSystem
     */
    public function __construct(
        WorkspaceAppRepository $workspaceAppRepository, ScannerAppRepository $scannerAppRepository,
        EntityManager $em, ScanIdentificationService $service
    )
    {
        $this->workspaceAppRepository = $workspaceAppRepository;
        $this->scannerAppRepository   = $scannerAppRepository;
        $this->service                = $service;
        $this->em                     = $em;
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
        $workspaceAppId  = $command->getId();
        $uploadedFile    = $command->getUploadedFile();
        $fileEntity      = $command->getFile();

        if (!isset($workspaceAppId, $uploadedFile)) {
            throw new InvalidInputException("One or more required members are not set on the command");
        }

        // Check that the given Workspace exists
        /** @var WorkspaceApp $workspaceApp */
        $workspaceApp = $this->workspaceAppRepository->find($workspaceAppId);
        if (empty($workspaceApp)) {
            throw new WorkspaceAppNotFoundException("There was no existing WorkspaceApp with the given ID");
        }

        // Check the authenticated User's permission
        if ($requestingUser->cannot(ComponentPolicy::ACTION_CREATE, $workspaceApp)) {
            throw new ActionNotPermittedException(
                "The authenticated User does not have permission to create new Assets on the given Workspace"
            );
        }

        // Intialise the scanner identification service and make sure the file is recognisable
        if (!$this->service->initialise($uploadedFile)) {
            throw new FileException("Could not match the file to any supported scanner output");
        }

        // Make sure the file is from the same scanner that is related to the WorkspaceApp
        $scanner = $this->service->getScanner();
        if ($scanner !== $workspaceApp->getScannerApp()->getName()) {
            throw new FileException(
                "The given file is not from the expected scanner app: {$workspaceApp->getScannerApp()->getName()}"
            );
        }

        // Store the file
        if (!$this->service->storeUploadedFile($workspaceAppId)) {
            throw new FileNotWritableException("Could not store uploaded file on server");
        }

        // Set the rest of the details on the File entity, persist it to the DB and return it
        $fileEntity->setPath(
            $this->service->getProvisionalStoragePath($workspaceAppId) . $uploadedFile->getClientOriginalName()
        );
        $fileEntity->setFormat($this->service->getFormat());
        $fileEntity->setSize($uploadedFile->getClientSize());
        $fileEntity->setUser($requestingUser);
        $fileEntity->setWorkspaceApp($workspaceApp);
        $fileEntity->setDeleted(false);
        $fileEntity->setProcessed(false);

        $this->em->persist($fileEntity);
        $this->em->flush($fileEntity);

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
}