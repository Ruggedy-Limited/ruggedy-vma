<?php

namespace App\Handlers\Commands;

use App\Commands\DeleteProject as DeleteProjectCommand;
use App\Entities\Base\AbstractEntity;
use App\Entities\Project;
use App\Entities\User;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\InvalidInputException;
use App\Exceptions\ProjectNotFoundException;
use App\Repositories\ProjectRepository;
use Doctrine\ORM\EntityManager;
use Exception;
use Illuminate\Support\Facades\Auth;
use stdClass;


class DeleteProject extends CommandHandler
{
    /** @var ProjectRepository */
    protected $projectRepository;
    
    /** @var EntityManager */
    protected $em;

    /**
     * DeleteProject constructor.
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
     * Process the DeleteProjectCommand
     *
     * @param DeleteProjectCommand $command
     * @return stdClass
     * @throws ActionNotPermittedException
     * @throws Exception
     * @throws InvalidInputException
     * @throws ProjectNotFoundException
     */
    public function handle(DeleteProjectCommand $command)
    {
        // Get the authenticated user
        /** @var User $requestingUser */
        $requestingUser = Auth::user();
        if (empty($requestingUser)) {
            throw new Exception("Could not get the authenticated user");
        }

        // Make sure that all the required members are set on the command
        $projectId = $command->getProjectId();
        if (!isset($projectId)) {
            throw new InvalidInputException("The required projectId member is not set on the command object");
        }

        // Check that the project exists
        /** @var Project $project */
        $project = $this->getProjectRepository()->find($projectId);
        if (empty($project)) {
            throw new ProjectNotFoundException("A Project with the given project ID was not found");
        }

        // Check that the User has permission to delete the Project
        if ($requestingUser->getId() !== $project->getUser()->getId()) {
            throw new ActionNotPermittedException("User does not have permission to delete this Project");
        }

        // If the deletion has been confirmed, then set the deleted flag on the Project and save to the database
        if ($command->isConfirm()) {
            $project->setDeleted(AbstractEntity::IS_DELETED);
            $this->getEm()->persist($project);
            $this->getEm()->flush($project);
        }

        return $project->toStdClass(['id', 'name', 'workspaces', 'deleted']);
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