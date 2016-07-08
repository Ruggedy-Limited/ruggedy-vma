<?php

namespace App\Handlers\Commands;

use App\Commands\UploadScanOutput as UploadScanOutputCommand;
use App\Entities\File;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\InvalidInputException;
use App\Exceptions\WorkspaceNotFoundException;
use App\Policies\ComponentPolicy;
use App\Repositories\WorkspaceRepository;
use Doctrine\ORM\EntityManager;
use Exception;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class UploadScanOutput extends CommandHandler
{
    const MIME_TYPE_XML = 'application/xml';

    /** @var WorkspaceRepository */
    protected $workspaceRepository;

    /** @var Filesystem */
    protected $fileSystem;

    /** @var EntityManager */
    protected $em;

    /**
     * UploadScanOutput constructor.
     *
     * @param WorkspaceRepository $workspaceRepository
     * @param Filesystem $fileSystem
     */
    public function __construct(WorkspaceRepository $workspaceRepository, Filesystem $fileSystem, EntityManager $em)
    {
        $this->workspaceRepository = $workspaceRepository;
        $this->fileSystem          = $fileSystem;
        $this->em                  = $em;
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
        $requestingUser = $this->authenticate();

        $workspaceId = $command->getId();
        $file        = $command->getFile();

        if (!isset($workspaceId, $file)) {
            throw new InvalidInputException("One or more required members are not set on the command");
        }

        $workspace = $this->getWorkspaceRepository()->find($workspaceId);
        if (empty($workspace)) {
            throw new WorkspaceNotFoundException("There was no existing Workspace with the given ID");
        }

        if ($requestingUser->cannot(ComponentPolicy::ACTION_CREATE, $workspace)) {
            throw new ActionNotPermittedException(
                "The authenticated User does not have permission to create new Assets on the given Workspace"
            );
        }

        if (!$file->isFile() || !$file->isValid() || !$file->isReadable()) {
            throw new FileException("The uploaded scan output file is not valid");
        }

        $fileType = $file->extension();
        $mimeType = $file->getClientMimeType();
        if (!empty($mimeType)) {
            $mimeParts = explode("/", $mimeType);
        }

        if (!empty($mimeParts) && is_array($mimeParts)) {
            $mimeExtension = array_pop($mimeParts);
        }

        if (!empty($mimeExtension) && $mimeExtension != $fileType) {
            $fileType = $mimeExtension;
        }

        if (!File::isValidFileType($fileType)) {
            throw new FileException("File of unsupported type '$fileType' given");
        }

        //TODO: Determine scan type based on file format
        $scanType = 'nmap';

        $storagePath = $this->getScanFileStorageBasePath() . $fileType . DIRECTORY_SEPARATOR
            . $scanType . DIRECTORY_SEPARATOR
            . $workspaceId;

        if (!$this->getFileSystem()->exists($storagePath)) {
            $this->getFileSystem()->mkdir($storagePath);
        }

        $file->move($storagePath, $file->getClientOriginalName());
        
        $fileEntity = new File();
        $fileEntity->setPath($storagePath . DIRECTORY_SEPARATOR . $file->getClientOriginalName());
        $fileEntity->setFormat($fileType);
        $fileEntity->setUser($requestingUser);
        $fileEntity->setWorkspace($workspace);
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
     * @return Filesystem
     */
    public function getFileSystem()
    {
        return $this->fileSystem;
    }

    /**
     * @return EntityManager
     */
    public function getEm()
    {
        return $this->em;
    }
}