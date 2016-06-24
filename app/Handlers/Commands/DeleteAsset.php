<?php

namespace App\Handlers\Commands;

use App\Commands\DeleteAsset as DeleteAssetCommand;
use App\Entities\Asset;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\AssetNotFoundException;
use App\Exceptions\InvalidInputException;
use App\Policies\ComponentPolicy;
use App\Repositories\AssetRepository;
use Doctrine\ORM\EntityManager;
use Exception;

class DeleteAsset extends CommandHandler
{
    /** @var AssetRepository */
    protected $assetRepository;

    /** @var EntityManager */
    protected $em;

    /**
     * DeleteAsset constructor.
     *
     * @param AssetRepository $assetRepository
     * @param EntityManager $em
     */
    public function __construct(AssetRepository $assetRepository, EntityManager $em)
    {
        $this->assetRepository = $assetRepository;
        $this->em              = $em;
    }

    /**
     * Process the DeleteAsset command.
     *
     * @param DeleteAssetCommand $command
     * @return Asset
     * @throws ActionNotPermittedException
     * @throws AssetNotFoundException
     * @throws InvalidInputException
     * @throws Exception
     */
    public function handle(DeleteAssetCommand $command)
    {
        // Get the authenticated User
        $requestingUser = $this->authenticate();

        $id      = $command->getId();
        $confirm = $command->isConfirm();

        // Check that we have the required input
        if (!isset($id, $confirm)) {
            throw new InvalidInputException("One or more required members are not set on the command");
        }

        // Get the asset instance from the database
        /** @var Asset $asset */
        $asset = $this->getAssetRepository()->find($id);
        if (empty($asset)) {
            throw new AssetNotFoundException("There is no existing Asset with the given Asset ID");
        }

        // Check that the authenticated User has permission to delete this Asset
        if ($requestingUser->cannot(ComponentPolicy::ACTION_DELETE, $asset)) {
            throw new ActionNotPermittedException(
                "The authenticated User does not have permission to delete the given Asset"
            );
        }

        // If deletion has been confirmed then set the deleted flag
        if ($command->isConfirm()) {
            $asset->setDeleted(true);
            $this->getEm()->persist($asset);
            $this->getEm()->flush($asset);
        }

        return $asset;
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