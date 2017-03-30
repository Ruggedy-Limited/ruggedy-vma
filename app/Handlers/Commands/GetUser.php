<?php

namespace App\Handlers\Commands;

use App\Commands\GetUser as GetUserCommand;
use App\Exceptions\InvalidInputException;
use App\Exceptions\UserNotFoundException;
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
     * @throws InvalidInputException
     * @throws UserNotFoundException
     */
    public function handle(GetUserCommand $command)
    {
        $userId = $command->getId();
        if (!isset($userId)) {
            throw new InvalidInputException("One or more required members are not set on the command");
        }

        $user = $this->repository->find($userId);
        if (empty($user)) {
            throw new UserNotFoundException("There is no existing User with the given ID");
        }

        return $user;
    }
}