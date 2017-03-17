<?php

namespace App\Handlers\Commands;

use App\Commands\GetFolder as GetFolderCommand;
use App\Entities\Folder;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\FolderNotFoundException;
use App\Policies\ComponentPolicy;
use App\Repositories\FolderRepository;

class GetFolder extends CommandHandler
{
    /** @var FolderRepository */
    protected $folderRepository;

    /**
     * GetFolder constructor.
     *
     * @param FolderRepository $folderRepository
     */
    public function __construct(FolderRepository $folderRepository)
    {
        $this->folderRepository = $folderRepository;
    }

    /**
     * Process the GetFolder command.
     *
     * @param GetFolderCommand $command
     * @return Folder
     * @throws ActionNotPermittedException
     * @throws FolderNotFoundException
     */
    public function handle(GetFolderCommand $command)
    {
        $requestingUser = $this->authenticate();

        /** @var Folder $folder */
        $folder = $this->folderRepository->find($command->getId());
        if (empty($folder)) {
            throw new FolderNotFoundException("No folder with the given ID was found.");
        }

        if ($requestingUser->cannot(ComponentPolicy::ACTION_VIEW, $folder->getWorkspace())) {
            throw new ActionNotPermittedException("The requesting User is not permitted to view this folder.");
        }

        return $folder;
    }
}