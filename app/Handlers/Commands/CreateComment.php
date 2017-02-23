<?php

namespace App\Handlers\Commands;

use App\Commands\CreateComment as CreateCommentCommand;
use App\Entities\File;
use App\Entities\Comment;
use App\Entities\Vulnerability;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\InvalidInputException;
use App\Exceptions\UserNotFoundException;
use App\Exceptions\FileNotFoundException;
use App\Exceptions\VulnerabilityNotFoundException;
use App\Exceptions\WorkspaceNotFoundException;
use App\Policies\ComponentPolicy;
use App\Repositories\VulnerabilityRepository;
use App\Repositories\CommentRepository;
use App\Repositories\FileRepository;
use Doctrine\ORM\EntityManager;
use Exception;

class CreateComment extends CommandHandler
{
    /** @var CommentRepository */
    protected $commentRepository;
    
    /** @var VulnerabilityRepository */
    protected $vulnerabilityRepository;

    /** @var FileRepository */
    protected $fileRepository;

    /** @var EntityManager */
    protected $em;

    /**
     * CreateAsset constructor.
     *
     * @param VulnerabilityRepository $assetRepository
     * @param CommentRepository $commentRepository
     * @param FileRepository $fileRepository
     * @param EntityManager $em
     */
    public function __construct(
        VulnerabilityRepository $assetRepository, CommentRepository $commentRepository,
        FileRepository $fileRepository, EntityManager $em
    )
    {
        $this->vulnerabilityRepository = $assetRepository;
        $this->commentRepository       = $commentRepository;
        $this->fileRepository          = $fileRepository;
        $this->em                      = $em;
    }

    /**
     * Process the CreateComment command.
     *
     * @param CreateCommentCommand $command
     * @return Comment
     * @throws InvalidInputException
     * @throws UserNotFoundException
     * @throws WorkspaceNotFoundException
     * @throws Exception
     */
    public function handle(CreateCommentCommand $command)
    {
        // Get the authenticated User
        $requestingUser = $this->authenticate();

        /** @var Comment $comment */
        $fileId          = $command->getId();
        $vulnerabilityId = $command->getVulnerabilityId();
        $comment         = $command->getEntity();
        // Check that all the required fields were set on the command
        if (!isset($fileId, $vulnerabilityId, $comment)) {
            throw new InvalidInputException("One or more required members are not set on the command");
        }

        /** @var File $file */
        $file = $this->fileRepository->find($fileId);
        // Make sure the File exists
        if (empty($file)) {
            throw new FileNotFoundException("A File with the given ID does not exist.");
        }

        /** @var Vulnerability $vulnerability */
        $vulnerability = $this->vulnerabilityRepository->find($vulnerabilityId);
        // Make sure the Vulnerability exists
        if (empty($vulnerability)) {
            throw new VulnerabilityNotFoundException("A Vulnerability with the given ID does not exist.");
        }

        // Make sure the User has permission to create a Comment
        if ($requestingUser->cannot(ComponentPolicy::ACTION_CREATE, $file)) {
            throw new ActionNotPermittedException(
                "The requesting User does not have permission to create new Comments"
            );
        }

        $comment->setUser($requestingUser)
            ->setFile($file)
            ->setVulnerability($vulnerability)
            ->setStatus(true);

        $this->em->persist($comment);
        // Save immediately if we're not in multi-mode
        if (!$command->isMultiMode()) {
            $this->em->flush($comment);
        }

        // Refresh and return the Comment
        $this->em->refresh($comment);
        return $comment;
    }
}