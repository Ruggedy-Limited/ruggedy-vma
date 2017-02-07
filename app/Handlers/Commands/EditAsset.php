<?php

namespace App\Handlers\Commands;

use App\Commands\EditAsset as EditAssetCommand;
use App\Entities\Asset;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\AssetNotFoundException;
use App\Exceptions\InvalidInputException;
use App\Policies\ComponentPolicy;
use App\Repositories\AssetRepository;
use Doctrine\ORM\EntityManager;
use Exception;

class EditAsset extends CommandHandler
{
    /** @var AssetRepository */
    protected $assetRepository;

    /** @var EntityManager */
    protected $em;

    /**
     * EditAsset constructor.
     *
     * @param AssetRepository $assetRepository
     * @param EntityManager $em
     */
    public function __construct(AssetRepository $assetRepository, EntityManager $em)
    {
        $this->assetRepository  = $assetRepository;
        $this->em               = $em;
    }

    /**
     * Process the EditAsset command
     *
     * @param EditAssetCommand $command
     * @return Asset
     * @throws ActionNotPermittedException
     * @throws AssetNotFoundException
     * @throws InvalidInputException
     * @throws Exception
     */
    public function handle(EditAssetCommand $command)
    {
        // Get the authenticated User
        $requestingUser = $this->authenticate();

        $id      = $command->getId();
        $changes = $command->getRequestedChanges();

        // Check that we have the required input
        if (!isset($id, $changes)) {
            throw new InvalidInputException("One or more required members are not set on the command");
        }

        // Get the asset instance from the database
        /** @var Asset $asset */
        $asset = $this->assetRepository->find($id);
        if (empty($asset)) {
            throw new AssetNotFoundException("There is no existing Asset with the given Asset ID");
        }

        // Check that the authenticated User has permission to edit this Asset
        if ($requestingUser->cannot(ComponentPolicy::ACTION_EDIT, $asset)) {
            throw new ActionNotPermittedException(
                "The authenticated User does not have permission to edit the given Asset"
            );
        }

        // Save the changes
        $asset->setFromArray($changes);
        $this->em->persist($asset);
        $this->em->flush($asset);
        $this->em->refresh($asset);

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