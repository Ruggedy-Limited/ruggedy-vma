<?php

namespace App\Handlers\Commands;

use App\Commands\CreateAsset as CreateAssetCommand;
use App\Entities\Asset;
use App\Entities\Workspace;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\InvalidInputException;
use App\Exceptions\UserNotFoundException;
use App\Exceptions\WorkspaceNotFoundException;
use App\Policies\ComponentPolicy;
use App\Repositories\AssetRepository;
use App\Repositories\WorkspaceRepository;
use Doctrine\ORM\EntityManager;
use Exception;

class CreateAsset extends CommandHandler
{
    /** @var WorkspaceRepository */
    protected $workspaceRepository;

    /** @var AssetRepository */
    protected $assetRepository;

    /** @var EntityManager */
    protected $em;

    /**
     * CreateAsset constructor.
     *
     * @param WorkspaceRepository $workspaceRepository
     * @param AssetRepository $assetRepository
     * @param EntityManager $em
     */
    public function __construct(
        WorkspaceRepository $workspaceRepository, AssetRepository $assetRepository, EntityManager $em
    )
    {
        $this->workspaceRepository = $workspaceRepository;
        $this->assetRepository     = $assetRepository;
        $this->em                  = $em;
    }

    /**
     * Process the CreateAsset command.
     *
     * @param CreateAssetCommand $command
     * @return Asset
     * @throws InvalidInputException
     * @throws UserNotFoundException
     * @throws WorkspaceNotFoundException
     * @throws Exception
     */
    public function handle(CreateAssetCommand $command)
    {
        // Get the authenticated User
        $requestingUser = $this->authenticate();
        
        // Check that all the required fields were set on the command
        $workspaceId = $command->getId();
        /** @var Asset $entity */
        $entity     = $command->getEntity();
        
        if (!isset($workspaceId, $entity)) {
            throw new InvalidInputException("One or more required members are not set on the command");
        }

        // Make sure the given Workspace exists
        /** @var Workspace $workspace */
        $workspace = $this->workspaceRepository->find($workspaceId);
        if (empty($workspace)) {
            throw new WorkspaceNotFoundException("No Workspace with the given ID was found in the database");
        }

        // Make sure the authenticated User has permission to add an Asset to this Workspace
        if ($requestingUser->cannot(ComponentPolicy::ACTION_CREATE, $workspace)) {
            throw new ActionNotPermittedException(
                "The authenticated User does not have permission to create an Asset on the given Workspace"
            );
        }

        // Set the Workspace ID
        $entity->setWorkspaceId($workspaceId);
        // Create a new Asset or find a matching existing Asset
        $asset = $this->assetRepository->findOrCreateOneBy($entity->toArray(true));

        // If this is a deleted or suppressed Asset then don't persist any changes but return the Asset as is
        if ($asset->getDeleted() || $asset->getSuppressed()) {
            return $asset;
        }

        $this->setAssetName($asset);
        $asset->setWorkspace($workspace);
        $asset->setUser($workspace->getUser());

        // Persist the new Asset to the database
        $this->em->persist($asset);
        $this->em->flush($asset);

        return $asset;
    }

    /**
     * Set the Asset name based on an order of precedence of non-null properties
     *
     * @param Asset $asset
     */
    protected function setAssetName(Asset $asset)
    {
        $assetName = $asset->getName() ?? $asset->getHostname() ?? $asset->getIpAddressV4()
            ?? Asset::ASSET_NAME_UNNAMED;
        $asset->setName($assetName);
    }

    /**
     * @return WorkspaceRepository
     */
    public function getWorkspaceRepository()
    {
        return $this->workspaceRepository;
    }

    /**
     * @return AssetRepository
     */
    public function getAssetRepository()
    {
        return $this->assetRepository;
    }

    /**
     * @return EntityManager
     */
    public function getEm()
    {
        return $this->em;
    }
}