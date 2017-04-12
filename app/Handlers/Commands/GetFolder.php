<?php

namespace App\Handlers\Commands;

use App\Commands\GetFolder as GetFolderCommand;
use App\Entities\Folder;
use App\Entities\Vulnerability;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\FolderNotFoundException;
use App\Policies\ComponentPolicy;
use App\Repositories\FolderRepository;
use App\Repositories\VulnerabilityRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class GetFolder extends CommandHandler
{
    /** @var FolderRepository */
    protected $folderRepository;

    /** @var VulnerabilityRepository */
    protected $vulnerabilityRepository;

    /**
     * GetFolder constructor.
     *
     * @param FolderRepository $folderRepository
     * @param VulnerabilityRepository $vulnerabilityRepository
     */
    public function __construct(FolderRepository $folderRepository, VulnerabilityRepository $vulnerabilityRepository)
    {
        $this->folderRepository        = $folderRepository;
        $this->vulnerabilityRepository = $vulnerabilityRepository;
    }

    /**
     * Process the GetFolder command.
     *
     * @param GetFolderCommand $command
     * @return array
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

        $vulnerabilities = $this->vulnerabilityRepository->findByFolderQuery($command->getId());

        return [
            'folder'                => $folder,
            Folder::VULNERABILITIES => $vulnerabilities,
        ];
    }
}