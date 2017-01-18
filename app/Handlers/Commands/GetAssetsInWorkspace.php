<?php

namespace App\Handlers\Commands;

use App\Commands\GetAssetsInWorkspace as GetAssetsInWorkspaceCommand;
use App\Entities\Workspace;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\InvalidInputException;
use App\Exceptions\WorkspaceNotFoundException;
use App\Policies\ComponentPolicy;
use App\Repositories\AssetRepository;
use App\Repositories\WorkspaceRepository;
use Doctrine\ORM\EntityManager;
use Exception;
use Illuminate\Support\Collection;

class GetAssetsInWorkspace extends CommandHandler
{
    /** @var AssetRepository */
    protected $assetRepository;

    /** @var WorkspaceRepository */
    protected $workspaceRepository;

    /** @var EntityManager */
    protected $em;

    /**
     * GetAssetsInWorkspace constructor.
     *
     * @param AssetRepository $assetRepository
     * @param WorkspaceRepository $workspaceRepository
     * @param EntityManager $em
     */
    public function __construct(
        AssetRepository $assetRepository, WorkspaceRepository $workspaceRepository, EntityManager $em
    )
    {
        $this->assetRepository     = $assetRepository;
        $this->workspaceRepository = $workspaceRepository;
        $this->em                  = $em;
    }

    /**
     * @param GetAssetsInWorkspaceCommand $command
     *
     * @return Collection
     * @throws ActionNotPermittedException
     * @throws InvalidInputException
     * @throws WorkspaceNotFoundException
     * @throws Exception
     */
    public function handle(GetAssetsInWorkspaceCommand $command)
    {
        $requestingUser = $this->authenticate();

        $workspaceId = $command->getId();
        if (!isset($workspaceId)) {
            throw new InvalidInputException("The required ID member is not set on the command");
        }

        /** @var Workspace $workspace */
        $workspace = $this->workspaceRepository->find($workspaceId);
        if (empty($workspace)) {
            throw new WorkspaceNotFoundException("There was no Workspace with the given ID in the database");
        }

        if ($requestingUser->cannot(ComponentPolicy::ACTION_LIST, $workspace)) {
            throw new ActionNotPermittedException(
                "The authenticated User does not have permission the list the Assets in this Workspace"
            );
        }

        return $workspace->getAssets();
    }
}