<?php

namespace App\Handlers\Commands;

use App\Commands\CreateAsset as CreateAssetCommand;
use App\Entities\Asset;
use App\Entities\File;
use App\Entities\Workspace;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\AssetNotFoundException;
use App\Exceptions\FileNotFoundException;
use App\Exceptions\InvalidInputException;
use App\Exceptions\UserNotFoundException;
use App\Exceptions\WorkspaceNotFoundException;
use App\Policies\ComponentPolicy;
use App\Repositories\AssetRepository;
use App\Repositories\FileRepository;
use Doctrine\ORM\EntityManager;
use Exception;

class CreateAsset extends CommandHandler
{
    /** @var FileRepository */
    protected $fileRepository;

    /** @var AssetRepository */
    protected $assetRepository;

    /** @var EntityManager */
    protected $em;

    /**
     * CreateAsset constructor.
     *
     * @param FileRepository $fileRepository
     * @param AssetRepository $assetRepository
     * @param EntityManager $em
     */
    public function __construct(
        FileRepository $fileRepository, AssetRepository $assetRepository, EntityManager $em
    )
    {
        $this->fileRepository  = $fileRepository;
        $this->assetRepository = $assetRepository;
        $this->em              = $em;
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
        $fileId = $command->getId();
        /** @var Asset $asset */
        $asset = $command->getEntity();
        
        if (!isset($fileId, $asset)) {
            throw new InvalidInputException("One or more required members are not set on the command");
        }

        // Make sure the given Workspace exists
        /** @var Workspace $file */
        $file = $this->fileRepository->find($fileId);
        if (empty($file) || !($file instanceof File) || $file->getDeleted() === true) {
            throw new FileNotFoundException("No File with the given ID was found in the database");
        }

        // Make sure the authenticated User has permission to add an Asset to this Workspace
        if ($requestingUser->cannot(ComponentPolicy::ACTION_CREATE, $file->getWorkspaceApp()->getWorkspace())) {
            throw new ActionNotPermittedException(
                "The authenticated User does not have permission to create an Asset on the given Workspace"
            );
        }

        $asset->setFileId($fileId);
        $asset->setFile($file);

        // Create a new Asset or find a matching existing Asset
        $asset = $this->assetRepository->findOrCreateOneBy($asset->toArray(true));
        if (empty($asset)) {
            throw new AssetNotFoundException("Could not find or create an Asset from the given input.");
        }

        // If this is a deleted or suppressed Asset then don't persist any changes but return the Asset as is
        if ($asset->getDeleted() || $asset->getSuppressed()) {
            return $asset;
        }

        // Set Asset name, as well as File and User relations
        $this->setAssetName($asset);
        $asset->setUser($file->getUser());

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
}