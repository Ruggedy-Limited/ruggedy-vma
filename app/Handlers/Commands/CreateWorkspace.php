<?php

namespace App\Handlers\Commands;

use App\Commands\CreateWorkspace as CreateWorkspaceCommand;
use App\Entities\User;
use App\Entities\Workspace;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\InvalidInputException;
use App\Exceptions\UserNotFoundException;
use App\Policies\ComponentPolicy;
use App\Repositories\UserRepository;
use Doctrine\ORM\EntityManager;
use Exception;

class CreateWorkspace extends CommandHandler
{
    /** @var UserRepository */
    protected $userRepository;

    /** @var EntityManager */
    protected $em;

    /**
     * CreateWorkspace constructor.
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
     * Process the CreateWorkspace command
     *
     * @param CreateWorkspaceCommand $command
     * @return Workspace
     * @throws ActionNotPermittedException
     * @throws Exception
     * @throws InvalidInputException
     * @throws UserNotFoundException
     */
    public function handle(CreateWorkspaceCommand $command)
    {
        // Get the authenticated User
        $requestingUser = $this->authenticate();

        // Make sure that all the required members are set on the command
        $userId = $command->getId();

        /** @var Workspace $workspace */
        $workspace = $command->getEntity();
        if (!isset($userId) || empty($workspace)) {
            throw new InvalidInputException("One or more of the required members are not set on the command object");
        }

        // Check that the parent Project exists
        /** @var User $user */
        $user = $this->userRepository->find($userId);
        if (empty($user)) {
            throw new UserNotFoundException("The User was not found or has been deleted");
        }

        // Check that the authenticated User has permission to create Workspace on the given Project
        if ($requestingUser->cannot(ComponentPolicy::ACTION_CREATE, $user)) {
            throw new ActionNotPermittedException("The authenticated user does not have permission to "
                . "create Workspaces on the given User account");
        }

        $workspace->setUser($user);
        $workspace->setDeleted(false);
        
        $this->em->persist($workspace);
        $this->em->flush($workspace);
        
        return $workspace;
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