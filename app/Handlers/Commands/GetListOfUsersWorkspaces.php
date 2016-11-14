<?php

namespace App\Handlers\Commands;

use App\Commands\GetListOfUsersWorkspaces as GetListOfUsersWorkspacesCommand;
use App\Entities\User;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\InvalidInputException;
use App\Exceptions\UserNotFoundException;
use App\Policies\ComponentPolicy;
use App\Repositories\UserRepository;
use Exception;


class GetListOfUsersWorkspaces extends CommandHandler
{
    /** @var UserRepository */
    protected $userRepository;

    /**
     * GetListOfUsersWorkspaces constructor.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Process the GetListOfUsersWorkspaces command
     *
     * @param GetListOfUsersWorkspacesCommand $command
     * @return array
     * @throws ActionNotPermittedException
     * @throws Exception
     * @throws InvalidInputException
     * @throws UserNotFoundException
     */
    public function handle(GetListOfUsersWorkspacesCommand $command)
    {
        // Get the authenticated User
        $requestingUser = $this->authenticate();

        // Make sure all the required members are set on the command
        $userId = $command->getId();
        if (!isset($userId)) {
            throw new InvalidInputException("The required User ID was not set on the given command");
        }

        /** @var User $user */
        $user = $this->userRepository->find($userId);
        if (empty($user)) {
            throw new UserNotFoundException("A User related to the given User ID was not found");
        }

        // Make sure the User has permission to list these Workspaces
        if ($requestingUser->cannot(ComponentPolicy::ACTION_LIST, $user)) {
            throw new ActionNotPermittedException(
                "The authenticated User does not have permission to list those Workspaces"
            );
        }

        return $user->getWorkspaces()->toArray();

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
}