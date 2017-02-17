<?php

namespace App\Handlers\Commands;

use App\Commands\DeleteFolder as DeleteFolderCommand;
use App\Entities\Folder;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\InvalidInputException;
use App\Exceptions\FolderNotFoundException;
use App\Policies\ComponentPolicy;
use App\Repositories\FolderRepository;
use Doctrine\ORM\EntityManager;
use Exception;

class DeleteFolder extends CommandHandler
{
    /** @var FolderRepository */
    protected $folderRepository;

    /** @var EntityManager */
    protected $em;

    /**
     * DeleteFolder constructor.
     * 
     * @param FolderRepository $folderRepository
     * @param EntityManager $em
     */
    public function __construct(FolderRepository $folderRepository, EntityManager $em)
    {
        $this->folderRepository = $folderRepository;
        $this->em                  = $em;
    }

    /**
     * Process the DeleteFolder command
     *
     * @param DeleteFolderCommand $command
     * @return Folder
     * @throws ActionNotPermittedException
     * @throws Exception
     * @throws InvalidInputException
     * @throws FolderNotFoundException
     */
    public function handle(DeleteFolderCommand $command)
    {
        // Get the authenticated user
        $requestingUser = $this->authenticate();

        // Make sure that all the required members are set on the command
        $folderId = $command->getId();
        if (!isset($folderId)) {
            throw new InvalidInputException("The required folderId member is not set on the command object");
        }

        // Check that the Folder exists
        /** @var Folder $folder */
        $folder = $this->folderRepository->find($folderId);
        if (empty($folder)) {
            throw new FolderNotFoundException("A Folder with the given folder ID was not found");
        }

        // Check that the User has permission to delete the Folder
        if ($requestingUser->cannot(ComponentPolicy::ACTION_DELETE, $folder)) {
            throw new ActionNotPermittedException("User does not have permission to delete this Folder");
        }

        // If the deletion has been confirmed, then delete the Folder from the database
        if ($command->isConfirm()) {
            $this->em->remove($folder);
            $this->em->flush($folder);
        }

        return $folder;
    }
}