<?php

namespace App\Handlers\Commands;

use App\Commands\GetComments as GetCommentsCommand;
use App\Entities\Comment;
use App\Exceptions\CommentNotFoundException;
use App\Exceptions\InvalidInputException;
use App\Repositories\CommentRepository;

class GetComments extends CommandHandler
{
    /** @var CommentRepository */
    protected $commentRepository;

    /**
     * GetComments constructor.
     *
     * @param CommentRepository $commentRepository
     */
    public function __construct(CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    /**
     * Process the GetComments command.
     *
     * @param GetCommentsCommand $command
     * @return array
     * @throws CommentNotFoundException
     * @throws InvalidInputException
     */
    public function handle(GetCommentsCommand $command)
    {
        $vulnerabilityId = $command->getId();
        if (!isset($vulnerabilityId)) {
            throw new InvalidInputException("One or more required members are not set on the command");
        }

        /** @var array $comments */
        $comments = $this->commentRepository->findBy([
            Comment::VULNERABILITY_ID => $vulnerabilityId
        ]);

        return $comments;
    }
}