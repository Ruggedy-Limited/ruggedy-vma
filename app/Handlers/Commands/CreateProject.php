<?php

namespace App\Handlers\Commands;

use App\Commands\CreateProject as CreateProjectCommand;
use App\Entities\Project;
use App\Entities\User;
use App\Exceptions\InvalidInputException;
use App\Exceptions\UserNotFoundException;
use App\Repositories\UserRepository;
use Doctrine\ORM\EntityManager;
use Exception;
use Illuminate\Support\Facades\Auth;
use stdClass;


class CreateProject
{
    
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
        /** @var User $requestingUser */
        $requestingUser = Auth::user();
        if (empty($requestingUser)) {
            throw new Exception("Could not get the authenticated user");
        }

        $userId = $command->getUserId();
        // Check that the required member is set on the command
        if (!isset($userId) || empty($command->getProjectDetails())) {
            throw new InvalidInputException("One or more required members were not set on the given command object");
        }
        
        $projectOwner = $requestingUser;
        if ($requestingUser->getId() !== $userId) {
            $projectOwner = $this->getUserRepository()->find($userId);
        }
        
        if (empty($projectOwner)) {
            throw new UserNotFoundException("A User related to the given user ID was not found");
        }

        $project = new Project();
        $project->setFromArray($command->getProjectDetails());
        $project->setUser($projectOwner);

        $requestingUser->addProject($project);

        $this->getEm()->persist($project);
        $this->getEm()->flush($project);

        return $project->toStdClass([
            'id', 'name'
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