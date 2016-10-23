<?php

namespace App\Handlers\Commands;

use App\Commands\GetAssetsInProject as GetAssetsInProjectCommand;
use App\Entities\Asset;
use App\Entities\Project;
use App\Entities\Workspace;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\InvalidInputException;
use App\Exceptions\ProjectNotFoundException;
use App\Exceptions\WorkspaceNotFoundException;
use App\Policies\ComponentPolicy;
use App\Repositories\ProjectRepository;
use Doctrine\ORM\EntityManager;
use Exception;
use Illuminate\Support\Collection;

class GetAssetsInProject extends CommandHandler
{
    /** @var ProjectRepository */
    protected $projectRepository;

    /** @var EntityManager */
    protected $em;

    /**
     * GetAssetsInWorkspace constructor.
     *
     * @param ProjectRepository $projectRepository
     * @param EntityManager $em
     */
    public function __construct(ProjectRepository $projectRepository, EntityManager $em)
    {
        $this->projectRepository = $projectRepository;
        $this->em                = $em;
    }

    /**
     * @param GetAssetsInProjectCommand $command
     *
     * @return Collection
     * @throws ActionNotPermittedException
     * @throws InvalidInputException
     * @throws WorkspaceNotFoundException
     * @throws Exception
     */
    public function handle(GetAssetsInProjectCommand $command)
    {
        // Get the authenticated User
        $requestingUser = $this->authenticate();

        // Make sure that the required members are set on the command
        $projectId = $command->getId();
        if (!isset($projectId)) {
            throw new InvalidInputException("The required ID member is not set on the command");
        }

        // Make sure the Project ecxists
        /** @var Project $project */
        $project = $this->projectRepository->find($projectId);
        if (empty($project)) {
            throw new ProjectNotFoundException("There was no Workspace with the given ID in the database");
        }

        // Check for permission to list the Assets
        if ($requestingUser->cannot(ComponentPolicy::ACTION_LIST, $project)) {
            throw new ActionNotPermittedException(
                "The authenticated User does not have permission the list the Assets in this Project"
            );
        }

        // Format the Assets into a single Collection and return them
        $assets = new Collection();
        $workspaces = new Collection($project->getWorkspaces()->toArray());
        $workspaces->map(function($workspace, $offset) use ($assets) {
            /** @var Workspace $workspace */
            $workspaceAssets = $workspace->getAssets();
            if (empty($workspaceAssets)) {
                return true;
            }

            // Iterate over the Workspace Assets and add each one to the Asset Collection
            $workspaceAssets->forAll(function($offset, $asset) use ($assets) {
                // Exclude Assets flagged as deleted
                /** @var Asset $asset */
                if ($asset->getDeleted()) {
                    return true;
                }

                $assets->push($asset);
                return true;
            });

            return true;
        });

        return $assets;
    }

    /**
     * @return ProjectRepository
     */
    public function getProjectRepository()
    {
        return $this->projectRepository;
    }

    /**
     * @return EntityManager
     */
    public function getEm()
    {
        return $this->em;
    }
}