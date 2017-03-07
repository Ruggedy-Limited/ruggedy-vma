<?php

namespace App\Handlers\Commands;

use App\Commands\GetListOfWorkspaces as GetListOfWorkspacesCommand;
use App\Entities\Workspace;
use App\Repositories\WorkspaceRepository;

class GetListOfWorkspaces extends CommandHandler
{
    protected $workspaceRepository;

    /**
     * GetListOfWorkspaces constructor.
     *
     * @param WorkspaceRepository $workspaceRepository
     */
    public function __construct(WorkspaceRepository $workspaceRepository)
    {
        $this->workspaceRepository = $workspaceRepository;
    }

    /**
     * Get all Workspaces
     *
     * @param GetListOfWorkspacesCommand $command
     * @return array
     */
    public function handle(GetListOfWorkspacesCommand $command)
    {
        $userId = $command->getId();
        if (empty($userId)) {
            return $this->workspaceRepository->findAll();
        }

        return $this->workspaceRepository->findBy([Workspace::USER_ID => $userId]);
    }
}