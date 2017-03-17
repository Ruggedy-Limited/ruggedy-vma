<?php

namespace App\Handlers\Commands;

use App\Commands\GetComment as GetCommentCommand;
use App\Entities\Comment;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\CommentNotFoundException;
use App\Policies\ComponentPolicy;
use App\Repositories\CommentRepository;

class GetComment extends CommandHandler
{
    /** @var CommentRepository */
    protected $commentRepository;

    /**
     * GetComment constructor.
     *
     * @param CommentRepository $commentRepository
     */
    public function __construct(CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    /**
     * Process the GetComment command.
     *
     * @param GetCommentCommand $command
     * @return Comment
     * @throws ActionNotPermittedException
     * @throws CommentNotFoundException
     */
    public function handle(GetCommentCommand $command)
    {
        /** @var Comment $comment */
        $comment = $this->commentRepository->find($command->getId());
        if (empty($comment)) {
            throw new CommentNotFoundException("No comment with the given ID was found.");
        }

        return $comment;
    }
}