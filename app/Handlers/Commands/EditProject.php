<?php

namespace App\Handlers\Commands;

use App\Commands\EditProject as EditProjectCommand;
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


class EditProject extends CommandHandler
{
    /** @var ProjectRepository */
    protected $projectRepository;
    
    /** @var EntityManager */
    protected $em;

    /**
     * EditProject constructor.
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
     * Process the EditProject command
     *
     * @param EditProjectCommand $command
     * @return Project
     * @throws ActionNotPermittedException
     * @throws Exception
     * @throws InvalidInputException
     * @throws ProjectNotFoundException
     */
    public function handle(EditProjectCommand $command)
    {
        // Get the authenticated User
        $requestingUser = $this->authenticate();

        // Make sure all the required members are set on the command
        $projectId        = $command->getId();
        $requestedChanges = $command->getRequestedChanges();
        if (!isset($projectId) || empty($requestedChanges)) {
            throw new InvalidInputException("One or more required members were not set on the command object");
        }

        // Make sure the Project exists
        /** @var Project $project */
        $project = $this->getProjectRepository()->find($projectId);
        if (empty($project) || !empty($project->getDeleted())) {
            throw new ProjectNotFoundException("The Project was not found or has been deleted");
        }

        // Make sure the authenticated User has permission to edit the Project
        if ($requestingUser->getId() !== $project->getUser()->getId()) {
            throw new ActionNotPermittedException("The User does not have permission to edit the Project");
        }

        // Set the changes on the Project entity and save it
        $project->setFromArray($requestedChanges);
        $this->getEm()->persist($project);
        $this->getEm()->flush($project);

        return $project;
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