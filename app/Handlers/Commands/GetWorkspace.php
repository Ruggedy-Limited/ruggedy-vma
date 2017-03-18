<?php

namespace App\Handlers\Commands;

use App\Commands\GetWorkspace as GetWorkspaceCommand;
use App\Entities\Workspace;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\InvalidInputException;
use App\Exceptions\WorkspaceNotFoundException;
use App\Policies\ComponentPolicy;
use App\Repositories\WorkspaceRepository;

class GetWorkspace extends CommandHandler
{
    /** @var WorkspaceRepository */
    protected $workspaceRepository;

    /**
     * GetListOfVulnerabilitiesInWorkspace constructor.
     *
     * @param WorkspaceRepository $workspaceRepository
     */
    public function __construct(WorkspaceRepository $workspaceRepository)
    {
        $this->workspaceRepository = $workspaceRepository;
    }

    /**
     * Process the GetListOfVulnerabilitiesInWorkspace command
     *
     * @param GetWorkspaceCommand $command
     * @return Workspace
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

        // Return the Workspace entity
        return $workspace;
    }

    /**
     * @return WorkspaceRepository
     */
    public function getWorkspaceRepository(): WorkspaceRepository
    {
        return $this->workspaceRepository;
    }
}