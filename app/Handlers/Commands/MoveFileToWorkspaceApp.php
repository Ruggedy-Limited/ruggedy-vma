<?php

namespace App\Handlers\Commands;

use App\Commands\MoveFileToWorkspaceApp as MoveFileToWorkspaceAppCommand;
use App\Entities\WorkspaceApp;
use App\Entities\File;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\WorkspaceAppNotFoundException;
use App\Exceptions\InvalidInputException;
use App\Exceptions\FileNotFoundException;
use App\Policies\ComponentPolicy;
use App\Repositories\WorkspaceAppRepository;
use App\Repositories\FileRepository;
use Doctrine\ORM\EntityManager;

class MoveFileToWorkspaceApp extends CommandHandler
{
    /** @var WorkspaceAppRepository */
    protected $workspaceAppRepository;

    /** @var FileRepository */
    protected $fileRepository;

    /** @var EntityManager */
    protected $em;

    /**
     * AddFileToWorkspaceApp constructor.
     *
     * @param WorkspaceAppRepository $workspaceAppRepository
     * @param FileRepository $fileRepository
     * @param EntityManager $em
     */
    public function __construct(
        WorkspaceAppRepository $workspaceAppRepository, FileRepository $fileRepository, EntityManager $em
    )
    {
        $this->workspaceAppRepository = $workspaceAppRepository;
        $this->fileRepository         = $fileRepository;
        $this->em                     = $em;
    }

    /**
     * Process the AddFileToWorkspaceAppCommand.
     *
     * @param MoveFileToWorkspaceAppCommand $command
     * @return WorkspaceApp
     * @throws ActionNotPermittedException
     * @throws WorkspaceAppNotFoundException
     * @throws InvalidInputException
     * @throws FileNotFoundException
     */
    public function handle(MoveFileToWorkspaceAppCommand $command)
    {
        $requestingUser = $this->authenticate();

        $workspaceAppId = $command->getWorkspaceAppId();
        $fileId         = $command->getFileId();
        if (!isset($workspaceAppId, $fileId)) {
            throw new InvalidInputException("One or more required members are not set on the command");
        }

        /** @var WorkspaceApp $workspaceApp */
        $workspaceApp = $this->workspaceAppRepository->find($workspaceAppId);
        if (empty($workspaceApp)) {
            throw new WorkspaceAppNotFoundException("A WorkspaceApp with the given ID does not exist");
        }

        /** @var File $file */
        $file = $this->fileRepository->find($fileId);
        if (empty($file)) {
            throw new FileNotFoundException("A File with the given ID does not exist");
        }

        if ($requestingUser->cannot(ComponentPolicy::ACTION_EDIT, $workspaceApp)) {
            throw new ActionNotPermittedException(
                "The requesting User does not have permission to add Vulnerabilities to the given WorkspaceApp"
            );
        }

        $workspaceApp->addFile($file);

        $this->em->persist($workspaceApp);
        $this->em->persist($file);
        $this->em->flush();

        return $workspaceApp;
    }
}