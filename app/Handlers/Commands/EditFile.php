<?php

namespace App\Handlers\Commands;

use App\Commands\EditFile as EditFileCommand;
use App\Entities\File;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\FileNotFoundException;
use App\Exceptions\InvalidInputException;
use App\Policies\ComponentPolicy;
use App\Repositories\FileRepository;
use Doctrine\ORM\EntityManager;

class EditFile extends CommandHandler
{
    /** @var FileRepository */
    protected $repository;

    /** @var EntityManager */
    protected $em;

    /**
     * EditFile constructor.
     *
     * @param FileRepository $repository
     * @param EntityManager $em
     */
    public function __construct(FileRepository $repository, EntityManager $em)
    {
        $this->repository = $repository;
        $this->em         = $em;
    }

    /**
     * Process the EditFile command.
     *
     * @param EditFileCommand $command
     * @return File
     * @throws ActionNotPermittedException
     * @throws FileNotFoundException
     * @throws InvalidInputException
     */
    public function handle(EditFileCommand $command)
    {
        // Get the authenticated User
        $requestingUser = $this->authenticate();

        // Check that we have everything we need to process the command
        $fileId      = $command->getId();
        $fileDetails = $command->getRequestedChanges();
        if (!isset($fileId, $fileDetails[File::NAME], $fileDetails[File::DESCRIPTION])) {
            throw new InvalidInputException("One or more required members are not set on the command");
        }

        /** @var File $file */
        // Check that the File exists
        $file = $this->repository->find($fileId);
        if (empty($file)) {
            throw new FileNotFoundException("There is no existing File with the given ID");
        }

        // Check that the requesting User has permission to edit this File's details
        if ($requestingUser->cannot(ComponentPolicy::ACTION_UPDATE, $file)) {
            throw new ActionNotPermittedException("The requesting User does not have permission to edit this File");
        }

        // Update the File details and persist the changes
        $file->setName($fileDetails[File::NAME])
            ->setDescription($fileDetails[File::DESCRIPTION]);

        $this->em->persist($file);
        $this->em->flush($file);

        return $file;
    }
}