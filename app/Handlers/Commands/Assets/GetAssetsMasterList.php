<?php

namespace App\Handlers\Commands\Assets;

use App\Commands\Assets\GetAssetsMasterList as GetAssetsMasterListCommand;
use App\Entities\Asset;
use App\Handlers\Commands\CommandHandler;
use App\Repositories\AssetRepository;
use Doctrine\ORM\EntityManager;
use Exception;

class GetAssetsMasterList extends CommandHandler
{
    /** @var AssetRepository  */
    protected $assetRepository;

    /** @var EntityManager  */
    protected $em;

    /**
     * GetAssetsMasterList constructor.
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
     * Handle the GetAssetsMasterList command
     *
     * @param GetAssetsMasterListCommand $command
     * @return array
     * @throws Exception
     */
    public function handle(GetAssetsMasterListCommand $command)
    {
        $requestingUser = $this->authenticate();

        if ($requestingUser->getAssets()->isEmpty()) {
            return [];
        }

        // No command parameters are needed as we are just
        // fetching all the assets owned by the authenticated User
        return $requestingUser->getAssets()->filter(function ($asset) {
            /** @var Asset $asset */
            // Exclude deleted Assets
             return $asset->getDeleted() !== true && $asset->getSuppressed() !== true;
        })->toArray();
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