<?php

namespace App\Handlers\Commands;

use App\Commands\GetWorkspace as GetWorkspaceCommand;
use App\Entities\Workspace;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\InvalidInputException;
use App\Exceptions\WorkspaceNotFoundException;
use App\Policies\ComponentPolicy;
use App\Repositories\FolderRepository;
use App\Repositories\WorkspaceAppRepository;
use App\Repositories\WorkspaceRepository;
use Illuminate\Support\Collection;

class GetWorkspace extends CommandHandler
{
    /** @var WorkspaceRepository */
    protected $workspaceRepository;

    /** @var WorkspaceAppRepository */
    protected $appRepository;

    /** @var FolderRepository */
    protected $folderRepository;

    /**
     * GetListOfVulnerabilitiesInWorkspace constructor.
     *
     * @param WorkspaceRepository $workspaceRepository
     * @param WorkspaceAppRepository $appRepository
     * @param FolderRepository $folderRepository
     */
    public function __construct(
        WorkspaceRepository $workspaceRepository, WorkspaceAppRepository $appRepository,
        FolderRepository $folderRepository
    )
    {
        $this->workspaceRepository = $workspaceRepository;
        $this->appRepository       = $appRepository;
        $this->folderRepository    = $folderRepository;
    }

    /**
     * Process the GetListOfVulnerabilitiesInWorkspace command
     *
     * @param GetWorkspaceCommand $command
     * @return Collection
     * @throws ActionNotPermittedException
     * @throws InvalidInputException
     * @throws WorkspaceNotFoundException
     */
    public function handle(GetWorkspaceCommand $command)
    {
        $requestingUser = $this->authenticate();
        $workspaceId    = $command->getId();

        // Check that the required members are set on the command
        if (!isset($workspaceId)) {
            throw new InvalidInputException("The required Workspace ID was not set on the command");
        }

        // Check that Workspace exists
        /** @var Workspace $workspace */
        $workspace = $this->workspaceRepository->findOneForWorkspaceView($workspaceId);
        if (empty($workspace)) {
            throw new WorkspaceNotFoundException("A Workspace with the given ID was not found");
        }

        // Check that the requesting User has permission to list the Vulnerabilities for the given Workspace
        if (!$requestingUser->can(ComponentPolicy::ACTION_VIEW, $workspace)) {
            throw new ActionNotPermittedException("The requesting User does not have permission to list "
                . "Vulnerabilities from that Workspace");
        }

        // Return the Workspace entity and paginated apps and folders
        return collect([
            'workspace' => $workspace,
            'apps'      => $this->appRepository->findByWorkspace($workspaceId),
            'folders'   => $this->folderRepository->findByWorkspace($workspaceId),
        ]);
    }

    /**
     * @return WorkspaceRepository
     */
    public function getWorkspaceRepository(): WorkspaceRepository
    {
        return $this->workspaceRepository;
    }
}