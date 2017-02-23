<?php

namespace App\Handlers\Commands;

use App\Commands\EditComment as EditCommentCommand;
use App\Entities\Comment;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\InvalidInputException;
use App\Exceptions\CommentNotFoundException;
use App\Repositories\CommentRepository;
use Doctrine\ORM\EntityManager;

class EditComment extends CommandHandler
{
    /** @var CommentRepository */
    protected $commentRepository;

    /** @var EntityManager */
    protected $em;

    /**
     * EditComment constructor.
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
     * Process the EditComment command.
     *
     * @param EditCommentCommand $command
     * @return Comment
     * @throws ActionNotPermittedException
     * @throws InvalidInputException
     * @throws CommentNotFoundException
     */
    public function handle(EditCommentCommand $command)
    {
        $requestingUser = $this->authenticate();

        /** @var Comment $comment */
        $commentId      = $command->getId();
        $commentDetails = $command->getRequestedChanges();

        // Make sure we have everything we need to process the command
        if (!isset($commentId, $commentDetails)) {
            throw new InvalidInputException("One or more required members are not set on the command");
        }

        // Make sure the relevant Comment exists
        $comment = $this->commentRepository->find($commentId);
        if (empty($comment)) {
            throw new CommentNotFoundException("No Comment with the given ID was found.");
        }

        // Make sure the requesting user is the owner of the comment
        if ($requestingUser->getId() !== $comment->getUser()->getId()) {
            throw new ActionNotPermittedException("Only the owner of a comment can edit it.");
        }

        // Set the amended details on the comment
        $comment->setFromArray($commentDetails);

        // Persist the changes to the database and refresh the entity state
        $this->em->persist($comment);
        $this->em->flush($comment);

        return $comment;
    }
}