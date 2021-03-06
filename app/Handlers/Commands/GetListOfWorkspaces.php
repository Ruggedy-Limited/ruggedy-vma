<?php

namespace App\Handlers\Commands;

use App\Commands\GetListOfWorkspaces as GetListOfWorkspacesCommand;
use App\Entities\Workspace;
use App\Repositories\WorkspaceRepository;

class GetListOfWorkspaces extends CommandHandler
{
    /** @var WorkspaceRepository */
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
     * Process the GetListOfWorkspaces command.
     *
     * @param GetListOfWorkspacesCommand $command
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function handle(GetListOfWorkspacesCommand $command)
    {
        return $this->workspaceRepository->findAllQuery($command->getId());
    }
}