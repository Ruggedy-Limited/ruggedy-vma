<?php

namespace App\Handlers\Commands;

use App\Commands\GetUser as GetUserCommand;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\InvalidInputException;
use App\Exceptions\UserNotFoundException;
use App\Policies\ComponentPolicy;
use App\Repositories\UserRepository;

class GetUser extends CommandHandler
{
    /** @var UserRepository */
    protected $repository;

    /**
     * GetUser constructor.
     *
     * @param UserRepository $repository
     */
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Process the GetUser command.
     *
     * @param GetUserCommand $command
     * @return null|object
     * @throws ActionNotPermittedException
     * @throws InvalidInputException
     * @throws UserNotFoundException
     */
    public function handle(GetUserCommand $command)
    {
        $requestingUser = $this->authenticate();

        // Make sure we have everything we need to process the command
        $userId = $command->getId();
        if (!isset($userId)) {
            throw new InvalidInputException("One or more required members are not set on the command");
        }

        // Make sure the User exists
        $user = $this->repository->find($userId);
        if (empty($user) || $user->isDeleted()) {
            throw new UserNotFoundException("There is no existing User with the given ID");
        }

        // Make sure the authenticated User can view the given User's profile details
        if ($requestingUser->cannot(ComponentPolicy::ACTION_VIEW, $user)) {
            throw new ActionNotPermittedException(
                "The authenticated User does not have permission to view the details of this User"
            );
        }

        return $user;
    }
}