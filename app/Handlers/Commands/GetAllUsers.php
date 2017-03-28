<?php

namespace App\Handlers\Commands;

use App\Commands\GetAllUsers as GetAllUsersCommand;
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
     * @return array
     */
    public function handle(GetAllUsersCommand $command)
    {
        return $this->repository->findAll();
    }
}