<?php

namespace App\Handlers\Commands;

use App\Commands\DeleteComment as DeleteCommentCommand;
use App\Entities\Comment;
use App\Entities\User;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\InvalidInputException;
use App\Exceptions\CommentNotFoundException;
use App\Policies\ComponentPolicy;
use App\Repositories\CommentRepository;
use Doctrine\ORM\EntityManager;
use Exception;

class DeleteComment extends CommandHandler
{
    /** @var CommentRepository */
    protected $commentRepository;

    /** @var EntityManager */
    protected $em;

    /**
     * DeleteComment constructor.
     * 
     * @param CommentRepository $commentRepository
     * @param EntityManager $em
     */
    public function __construct(CommentRepository $commentRepository, EntityManager $em)
    {
        $this->commentRepository = $commentRepository;
        $this->em                = $em;
    }

    /**
     * Process the DeleteComment command
     *
     * @param DeleteCommentCommand $command
     * @return Comment
     * @throws ActionNotPermittedException
     * @throws Exception
     * @throws InvalidInputException
     * @throws CommentNotFoundException
     */
    public function handle(DeleteCommentCommand $command)
    {
        // Get the authenticated user
        $requestingUser = $this->authenticate();

        // Make sure that all the required members are set on the command
        $commentId = $command->getId();
        if (!isset($commentId)) {
            throw new InvalidInputException("The required commentId member is not set on the command object");
        }

        // Check that the Comment exists
        /** @var Comment $comment */
        $comment = $this->commentRepository->find($commentId);
        if (empty($comment)) {
            throw new CommentNotFoundException("A Comment with the given comment ID was not found");
        }

        // Check that the User has permission to delete the Comment
        if ($requestingUser->isAdmin() && $requestingUser->getId() !== $comment->getUser()->getId()) {
            throw new ActionNotPermittedException("User does not have permission to delete this Comment");
        }

        // If the deletion has been confirmed, then delete the Comment from the database
        if ($command->isConfirm()) {
            $this->em->remove($comment);
            $this->em->flush($comment);
        }

        return $comment;
    }
}