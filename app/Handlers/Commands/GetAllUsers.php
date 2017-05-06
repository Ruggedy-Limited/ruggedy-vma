<?php

namespace App\Handlers\Commands;

use App\Commands\GetAllUsers as GetAllUsersCommand;
use App\Entities\User;
use App\Exceptions\ActionNotPermittedException;
use App\Policies\ComponentPolicy;
use App\Repositories\UserRepository;

class GetAllUsers extends CommandHandler
{
    /** @var UserRepository */
    protected $repository;

    /**
     * GetAllUsers constructor.
     *
     * @param UserRepository $repository
     */
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Process the GetAllUsers command.
     *
     * @param GetAllUsersCommand $command
     * @return \Illuminate\Pagination\LengthAwarePaginator
     * @throws ActionNotPermittedException
     */
    public function handle(GetAllUsersCommand $command)
    {
        $requestingUser = $this->authenticate();

        if ($requestingUser->cannot(ComponentPolicy::ACTION_LIST, new User())) {
            throw new ActionNotPermittedException("User does not have permission to list other Users on the system.");
        }

        return $this->repository->findAllButCurrentUserQuery($requestingUser);
    }
}