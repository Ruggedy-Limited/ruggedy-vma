<?php

namespace App\Handlers\Commands;

use App\Commands\GetWorkspaceApp as GetWorkspaceAppCommand;
use App\Entities\WorkspaceApp;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\WorkspaceAppNotFoundException;
use App\Policies\ComponentPolicy;
use App\Repositories\WorkspaceAppRepository;

class GetWorkspaceApp extends CommandHandler
{
    /** @var WorkspaceAppRepository */
    protected $workspaceAppRepository;

    /**
     * GetWorkspaceApp constructor.
     *
     * @param WorkspaceAppRepository $workspaceAppRepository
     */
    public function __construct(WorkspaceAppRepository $workspaceAppRepository)
    {
        $this->workspaceAppRepository = $workspaceAppRepository;
    }

    /**
     * Process the GetWorkspaceApp command.
     *
     * @param GetWorkspaceAppCommand $command
     * @return WorkspaceApp
     * @throws ActionNotPermittedException
     * @throws WorkspaceAppNotFoundException
     */
    public function handle(GetWorkspaceAppCommand $command)
    {
        $requestingUser = $this->authenticate();

        /** @var WorkspaceApp $workspaceApp */
        $workspaceApp = $this->workspaceAppRepository->find($command->getId());
        if (empty($workspaceApp)) {
            throw new WorkspaceAppNotFoundException("No WorkspaceApp with the given ID was found.");
        }

        if ($requestingUser->cannot(ComponentPolicy::ACTION_VIEW, $workspaceApp->getWorkspace())) {
            throw new ActionNotPermittedException("The requesting User is not permitted to view this WorkspaceApp.");
        }

        return $workspaceApp;
    }
}