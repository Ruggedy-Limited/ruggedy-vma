<?php

namespace App\Handlers\Commands;

use App\Commands\GetListOfAppsInWorkspace as GetListOfAppsInWorkspaceCommand;
use App\Entities\File;
use App\Entities\Workspace;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\InvalidInputException;
use App\Exceptions\WorkspaceNotFoundException;
use App\Policies\ComponentPolicy;
use App\Repositories\WorkspaceRepository;
use Illuminate\Support\Collection;

class GetListOfAppsInWorkspace extends CommandHandler
{
    /** @var WorkspaceRepository */
    protected $workspaceRepository;

    /**
     * GetListOfAppsInWorkspace constructor.
     *
     * @param WorkspaceRepository $workspaceRepository
     */
    public function __construct(WorkspaceRepository $workspaceRepository)
    {
        $this->workspaceRepository = $workspaceRepository;
    }

    /**
     * Process the GetListOfAppsInWorkspace command
     *
     * @param GetListOfAppsInWorkspaceCommand $command
     * @return Collection
     * @throws ActionNotPermittedException
     * @throws InvalidInputException
     * @throws WorkspaceNotFoundException
     */
    public function handle(GetListOfAppsInWorkspaceCommand $command)
    {
        $requestingUser = $this->authenticate();
        $workspaceId = $command->getId();

        // Check that the required members are set on the command object
        if (!isset($workspaceId)) {
            throw new InvalidInputException("The required Workspace ID is not set on the Command");
        }

        // Make sure the Workspace exists
        /** @var Workspace $workspace */
        $workspace = $this->workspaceRepository->find($workspaceId);
        if (empty($workspace)) {
            throw new WorkspaceNotFoundException("There was no Workspace with the given ID in the database");
        }

        // Check that the requesting User has the relevant permissions
        if ($requestingUser->cannot(ComponentPolicy::ACTION_LIST, $workspace)) {
            throw new ActionNotPermittedException(
                "The authenticated User does not have permission the list the Apps in this Workspace"
            );
        }

        // Return a unique Collection of Apps
        return collect(
            $workspace->getFiles()->map(function ($file) {
                /** @var File $file */
                return $file->getScannerApp();
            })->toArray()
        )->unique();
    }

    /**
     * @return WorkspaceRepository
     */
    public function getWorkspaceRepository(): WorkspaceRepository
    {
        return $this->workspaceRepository;
    }
}