<?php

namespace App\Handlers\Commands;

use App\Commands\CreateWorkspace as CreateWorkspaceCommand;
use App\Entities\Base\AbstractEntity;
use App\Entities\Project;
use App\Entities\User;
use App\Entities\Workspace;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\InvalidInputException;
use App\Exceptions\ProjectNotFoundException;
use App\Repositories\ProjectRepository;
use Doctrine\ORM\EntityManager;
use Exception;
use Illuminate\Support\Facades\Auth;


class CreateWorkspace extends CommandHandler
{
    /** @var ProjectRepository */
    protected $projectRepository;

    /** @var EntityManager */
    protected $em;

    /**
     * CreateWorkspace constructor.
     * 
     * @param ProjectRepository $projectRepository
     * @param EntityManager $em
     */
    public function __construct(ProjectRepository $projectRepository, EntityManager $em)
    {
        $this->projectRepository = $projectRepository;
        $this->em                = $em;
    }

    /**
     * Process the CreateWorkspace command
     *
     * @param CreateWorkspaceCommand $command
     * @return Workspace
     * @throws ActionNotPermittedException
     * @throws Exception
     * @throws InvalidInputException
     * @throws ProjectNotFoundException
     */
    public function handle(CreateWorkspaceCommand $command)
    {
        // Get the authenticated User
        /** @var User $requestingUser */
        $requestingUser = Auth::user();
        if (empty($requestingUser)) {
            throw new Exception("Could not get an authenticated User");
        }

        // Make sure that all the required members are set on the command
        $projectId        = $command->getProjectId();
        $workspaceDetails = $command->getWorkspaceDetails();
        if (!isset($projectId) || empty($workspaceDetails)) {
            throw new InvalidInputException("One or more of the required members are not set on the command object");
        }

        // Check that the Project exists
        /** @var Project $project */
        $project = $this->getProjectRepository()->find($projectId);
        if (empty($project) || $project->getDeleted() === AbstractEntity::IS_DELETED) {
            throw new ProjectNotFoundException("The Project was not found or has been deleted");
        }

        // Check that the authenticated User has permission to create Workspace or the given Project
        if ($requestingUser->getId() !== $project->getUser()->getId()) {
            throw new ActionNotPermittedException("The authenticated user does not have permission to "
                . "create Workspaces for the given Project");
        }

        $workspace = new Workspace();
        $workspace->setFromArray($workspaceDetails);
        $workspace->setUser($requestingUser);
        $workspace->setProject($project);
        $workspace->setDeleted(AbstractEntity::NOT_DELETED);
        
        $this->getEm()->persist($workspace);
        $this->getEm()->flush($workspace);
        
        return $workspace;
    }

    /**
     * @return ProjectRepository
     */
    public function getProjectRepository()
    {
        return $this->projectRepository;
    }

    /**
     * @param ProjectRepository $projectRepository
     */
    public function setProjectRepository($projectRepository)
    {
        $this->projectRepository = $projectRepository;
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