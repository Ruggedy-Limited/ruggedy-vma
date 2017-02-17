<?php

namespace App\Handlers\Commands;

use App\Commands\EditFolder as EditFolderCommand;
use App\Entities\Folder;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\InvalidInputException;
use App\Exceptions\FolderNotFoundException;
use App\Policies\ComponentPolicy;
use App\Repositories\FolderRepository;
use Doctrine\ORM\EntityManager;

class EditFolder extends CommandHandler
{
    /** @var FolderRepository */
    protected $folderRepository;

    /** @var EntityManager */
    protected $em;

    /**
     * CreateFolder constructor.
     *
     * @param FolderRepository $folderRepository
     * @param EntityManager $em
     */
    public function __construct(FolderRepository $folderRepository, EntityManager $em)
    {
        $this->folderRepository = $folderRepository;
        $this->em               = $em;
    }

    /**
     * Process the CreateFolder command.
     *
     * @param EditFolderCommand $command
     * @return Folder
     * @throws ActionNotPermittedException
     * @throws InvalidInputException
     * @throws FolderNotFoundException
     */
    public function handle(EditFolderCommand $command)
    {
        $requestingUser = $this->authenticate();

        $folderId   = $command->getId();
        /** @var Folder $folder */
        $folderDetails = $command->getRequestedChanges();

        // Make sure we have everything we need to process the command
        if (!isset($folderId, $folderDetails)) {
            throw new InvalidInputException("One or more required members are not set on the command");
        }

        // Make sure the relevant Workspace exists
        $folder = $this->folderRepository->find($folderId);
        if (empty($folder)) {
            throw new FolderNotFoundException("No Folder with the given ID was found.");
        }

        // Make sure the requesting user has permission to perform this action
        if ($requestingUser->cannot(ComponentPolicy::ACTION_EDIT, $folder)) {
            throw new ActionNotPermittedException(
                "The requesting User does not have permission to edit the given Folder"
            );
        }

        // Set the amended details on the folder
        $folder->setFromArray($folderDetails);

        // Persist the changes to the database and refresh the entity state
        $this->em->persist($folder);
        $this->em->flush($folder);

        return $folder;
    }
}