<?php

namespace App\Handlers\Commands;

use App\Commands\GetNewComments as GetNewCommentsCommand;
use App\Exceptions\InvalidInputException;
use App\Repositories\CommentRepository;

class GetNewComments extends CommandHandler
{
    /** @var CommentRepository */
    protected $repository;

    /**
     * GetNewComments constructor.
     *
     * @param CommentRepository $repository
     */
    public function __construct(CommentRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Process the GetNewComments command.
     *
     * @param GetNewCommentsCommand $command
     * @return array
     * @throws InvalidInputException
     */
    public function handle(GetNewCommentsCommand $command)
    {
        $vulnerabilityId = $command->getId();
        $newerThan       = $command->getNewerThan();
        if (!isset($vulnerabilityId, $newerThan)) {
            throw new InvalidInputException("One or more required members are not set on the command.");
        }

        return $this->repository->findNewComments($vulnerabilityId, $newerThan);
    }
}