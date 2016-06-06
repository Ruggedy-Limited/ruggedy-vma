<?php

namespace App\Handlers\Commands;

use App\Commands\DeleteWorkspace as DeleteWorkspaceCommand;
use App\Entities\Base\AbstractEntity;
use App\Entities\User;
use App\Entities\Workspace;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\InvalidInputException;
use App\Exceptions\WorkspaceNotFoundException;
use App\Repositories\WorkspaceRepository;
use Doctrine\ORM\EntityManager;
use Exception;
use Illuminate\Support\Facades\Auth;
use stdClass;


class DeleteWorkspace extends CommandHandler
{
    protected $workspaceRepository;
    
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
        /** @var User $requestingUser */
        $requestingUser = Auth::user();
        if (empty($requestingUser)) {
            throw new Exception("Could not get the authenticated user");
        }

        // Make sure that all the required members are set on the command
        $workspaceId = $command->getWorkspaceId();
        if (!isset($workspaceId)) {
            throw new InvalidInputException("The required projectId member is not set on the command object");
        }

        // Check that the project exists
        /** @var Workspace $workspace */
        $workspace = $this->getWorkspaceRepository()->find($workspaceId);
        if (empty($workspace)) {
            throw new WorkspaceNotFoundException("A Project with the given project ID was not found");
        }

        // Check that the User has permission to delete the Project
        if ($requestingUser->getId() !== $workspace->getUser()->getId()) {
            throw new ActionNotPermittedException("User does not have permission to delete this Project");
        }

        // If the deletion has been confirmed, then set the deleted flag on the Project and save to the database
        if ($command->isConfirm()) {
            $workspace->setDeleted(AbstractEntity::IS_DELETED);
            $this->getEm()->persist($workspace);
            $this->getEm()->flush($workspace);
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