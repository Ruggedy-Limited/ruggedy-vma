<?php

namespace App\Handlers\Commands;

use App\Commands\DeleteWorkspace as DeleteWorkspaceCommand;
use App\Entities\Workspace;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\InvalidInputException;
use App\Exceptions\WorkspaceNotFoundException;
use App\Policies\ComponentPolicy;
use App\Repositories\WorkspaceRepository;
use Doctrine\ORM\EntityManager;
use Exception;
use stdClass;


class DeleteWorkspace extends CommandHandler
{
    /** @var WorkspaceRepository */
    protected $workspaceRepository;

    /** @var EntityManager */
    protected $em;

    /**
     * DeleteWorkspace constructor.
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
     * Process the DeleteWorkspace command
     *
     * @param DeleteWorkspaceCommand $command
     * @return stdClass
     * @throws ActionNotPermittedException
     * @throws Exception
     * @throws InvalidInputException
     * @throws WorkspaceNotFoundException
     */
    public function handle(DeleteWorkspaceCommand $command)
    {
        // Get the authenticated user
        $requestingUser = $this->authenticate();

        // Make sure that all the required members are set on the command
        $workspaceId = $command->getId();
        if (!isset($workspaceId)) {
            throw new InvalidInputException("The required projectId member is not set on the command object");
        }

        // Check that the Workspace exists
        /** @var Workspace $workspace */
        $workspace = $this->workspaceRepository->find($workspaceId);
        if (empty($workspace) || !empty($workspace->getDeleted())) {
            throw new WorkspaceNotFoundException("A Project with the given project ID was not found");
        }

        // Check that the User has permission to delete the Workspace
        if ($requestingUser->cannot(ComponentPolicy::ACTION_DELETE, $workspace)) {
            throw new ActionNotPermittedException("User does not have permission to delete this Workspace");
        }

        // If the deletion has been confirmed, then set the deleted flag on the Workspace and save to the database
        if ($command->isConfirm()) {
            $workspace->setDeleted(true);
            $this->em->persist($workspace);
            $this->em->flush($workspace);
        }

        return $workspace->toStdClass(['id', 'name', 'deleted', 'created_at', 'updated_at']);
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