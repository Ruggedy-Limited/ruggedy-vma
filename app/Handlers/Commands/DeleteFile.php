<?php

namespace App\Handlers\Commands;

use App\Commands\DeleteFile as DeleteFileCommand;
use App\Entities\File;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\FileNotFoundException;
use App\Policies\ComponentPolicy;
use App\Repositories\FileRepository;
use Doctrine\ORM\EntityManager;

class DeleteFile extends CommandHandler
{
    /** @var FileRepository */
    protected $fileRepository;

    /** @var EntityManager */
    protected $em;

    /**
     * DeleteFile constructor.
     *
     * @param FileRepository $fileRepository
     * @param EntityManager $em
     */
    public function __construct(FileRepository $fileRepository, EntityManager $em)
    {
        $this->fileRepository = $fileRepository;
        $this->em             = $em;
    }

    /**
     * Handle the DeleteFile command.
     *
     * @param DeleteFileCommand $command
     * @return File
     * @throws ActionNotPermittedException
     * @throws FileNotFoundException
     */
    public function handle(DeleteFileCommand $command)
    {
        $requestingUser = $this->authenticate();

        /** @var File $file */
        $file = $this->fileRepository->find($command->getId());
        if (empty($file)) {
            throw new FileNotFoundException("No existing File with the given ID was found.");
        }

        // Check that the User has permission to delete the File
        if ($requestingUser->cannot(ComponentPolicy::ACTION_DELETE, $file)) {
            throw new ActionNotPermittedException("User does not have permission to delete this File");
        }

        if ($command->isConfirm()) {
            $this->em->remove($file);
            $this->em->flush($file);
        }

        return $file;
    }
}