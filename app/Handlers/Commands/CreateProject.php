<?php

namespace App\Handlers\Commands;

use App\Commands\CreateProject as CreateProjectCommand;
use App\Entities\Project;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\InvalidInputException;
use App\Exceptions\UserNotFoundException;
use App\Policies\ComponentPolicy;
use App\Repositories\UserRepository;
use Doctrine\ORM\EntityManager;
use Exception;
use stdClass;


class CreateProject extends CommandHandler
{
    /** @var UserRepository */
    protected $userRepository;
    
    /** @var EntityManager */
    protected $em;

    /**
     * CreateProject constructor.
     *
     * @param UserRepository $userRepository
     * @param EntityManager $em
     */
    public function __construct(UserRepository $userRepository, EntityManager $em)
    {
        $this->userRepository = $userRepository;
        $this->em             = $em;
    }

    /**
     * Process the CreateProject command
     *
     * @param CreateProjectCommand $command
     * @return stdClass
     * @throws Exception
     * @throws InvalidInputException
     */
    public function handle(CreateProjectCommand $command)
    {
        // Get the authenticated user
        $requestingUser = $this->authenticate();

        $userId         = $command->getId();
        $projectDetails = $command->getDetails();

        // Check that the required member is set on the command
        if (!isset($userId) || empty($projectDetails)) {
            throw new InvalidInputException("One or more required members were not set on the given command object");
        }

        // Assign the Project Owner
        $projectOwner = $requestingUser;
        if ($requestingUser->getId() !== $userId) {
            $projectOwner = $this->getUserRepository()->find($userId);
        }

        // Exit if the Project owner does not exist in the database
        if (empty($projectOwner)) {
            throw new UserNotFoundException("A User related to the given user ID was not found");
        }

        // Check that the authenticated user has permission to create a Project on the given User account
        if ($requestingUser->cannot(ComponentPolicy::ACTION_CREATE, $projectOwner)) {
            throw new ActionNotPermittedException(
                "The authenticated User does not have permission to create a Project on this User account"
            );
        }

        // Create a new Project entity
        $project = new Project();
        $project->setFromArray($projectDetails);
        $project->setUser($projectOwner);
        $project->setUserId($projectOwner->getId());
        $project->setDeleted(false);

        // Add the project to the user in the current session
        $requestingUser->addProject($project);

        // Persist the Project in the Database
        $this->getEm()->persist($project);
        $this->getEm()->flush($project);

        return $project->toStdClass([
            'id', 'name', 'user_id'
        ]);
    }

    /**
     * @return UserRepository
     */
    public function getUserRepository()
    {
        return $this->userRepository;
    }

    /**
     * @param UserRepository $userRepository
     */
    public function setUserRepository($userRepository)
    {
        $this->userRepository = $userRepository;
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