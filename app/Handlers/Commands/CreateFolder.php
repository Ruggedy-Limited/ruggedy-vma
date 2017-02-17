<?php

namespace App\Handlers\Commands;

use App\Commands\CreateFolder as CreateFolderCommand;
use App\Entities\Folder;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\InvalidInputException;
use App\Exceptions\WorkspaceNotFoundException;
use App\Policies\ComponentPolicy;
use App\Repositories\WorkspaceRepository;
use Doctrine\ORM\EntityManager;

class CreateFolder extends CommandHandler
{
    /** @var WorkspaceRepository */
    protected $workspaceRepository;

    /** @var EntityManager */
    protected $em;

    /**
     * CreateFolder constructor.
     *
     * @param WorkspaceRepository $workspaceRepository
     * @param EntityManager $em
     */
    public function __construct(WorkspaceRepository $workspaceRepository, EntityManager $em)
    {
        $this->workspaceRepository = $workspaceRepository;
        $this->em                  = $em;
    }

    /**
     * Process the CreateFolder command.
     *
     * @param CreateFolderCommand $command
     * @return Folder
     * @throws ActionNotPermittedException
     * @throws InvalidInputException
     * @throws WorkspaceNotFoundException
     */
    public function handle(CreateFolderCommand $command)
    {
        $requestingUser = $this->authenticate();

        $workspaceId = $command->getId();
        /** @var Folder $folder */
        $folder      = $command->getEntity();

        // Make sure we have everything we need to process the command
        if (!isset($workspaceId, $folder)) {
            throw new InvalidInputException("One or more required members are not set on the command");
        }

        // Make sure the relevant Workspace exists
        $workspace = $this->workspaceRepository->find($workspaceId);
        if (empty($workspace)) {
            throw new WorkspaceNotFoundException("No Workspace with the given ID was found.");
        }

        // Make sure the requesting user has permission to perform this action
        if ($requestingUser->cannot(ComponentPolicy::ACTION_CREATE, $workspace)) {
            throw new ActionNotPermittedException(
                "The requesting User does not have permission to create Folders on the given Workspace"
            );
        }

        // Set the Workspace and the User on the Folder
        $folder->setWorkspace($workspace);
        $folder->setUser($requestingUser);

        // Persist the changes to the database and refresh the entity state
        $this->em->persist($folder);
        $this->em->flush($folder);
        $this->em->refresh($folder);

        return $folder;
    }
}