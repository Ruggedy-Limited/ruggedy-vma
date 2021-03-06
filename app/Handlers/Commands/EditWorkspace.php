<?php

namespace App\Handlers\Commands;

use App\Commands\EditWorkspace as EditWorkspaceCommand;
use App\Entities\Workspace;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\InvalidInputException;
use App\Exceptions\WorkspaceNotFoundException;
use App\Policies\ComponentPolicy;
use App\Repositories\WorkspaceRepository;
use Doctrine\ORM\EntityManager;
use Exception;


class EditWorkspace extends CommandHandler
{
    /** @var WorkspaceRepository */
    protected $workspaceRepository;
    
    /** @var EntityManager */
    protected $em;

    /**
     * EditWorkspace constructor.
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
     * Process the EditWorkspace command
     *
     * @param EditWorkspaceCommand $command
     * @return Workspace
     * @throws ActionNotPermittedException
     * @throws Exception
     * @throws InvalidInputException
     * @throws WorkspaceNotFoundException
     */
    public function handle(EditWorkspaceCommand $command)
    {
        // Get the authenticated User
        $requestingUser = $this->authenticate();

        // Make sure all the required members are set on the command
        $workspaceId      = $command->getId();
        $requestedChanges = $command->getRequestedChanges();
        if (!isset($workspaceId) || empty($requestedChanges)) {
            throw new InvalidInputException("One or more required members were not set on the command object");
        }

        // Make sure the Workspace exists
        /** @var Workspace $workspace */
        $workspace = $this->workspaceRepository->find($workspaceId);
        if (empty($workspace) || !empty($workspace->getDeleted())) {
            throw new WorkspaceNotFoundException("The Workspace was not found or has been deleted");
        }

        // Make sure the authenticated User has permission to edit the Workspace
        if ($requestingUser->cannot(ComponentPolicy::ACTION_EDIT, $workspace)) {
            throw new ActionNotPermittedException("The User does not have permission to edit the Workspace");
        }

        // Set the changes on the Workspace entity and save it
        $workspace->setFromArray($requestedChanges);
        $this->em->persist($workspace);
        $this->em->flush($workspace);
        $this->em->refresh($workspace);

        return $workspace;
    }

    /**
     * @return WorkspaceRepository
     */
    public function getWorkspaceRepository()
    {
        return $this->workspaceRepository;
    }

    /**
     * @param WorkspaceRepository $workspaceRepository
     */
    public function setWorkspaceRepository($workspaceRepository)
    {
        $this->workspaceRepository = $workspaceRepository;
    }

    /**
     * @return EntityManager
     */
    public function getEm()
    {
        return $this->em;
    }

    /**
     * @param EntityManager $em
     */
    public function setEm($em)
    {
        $this->em = $em;
    }
}