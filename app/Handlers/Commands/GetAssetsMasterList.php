<?php

namespace App\Handlers\Commands;

use App\Commands\GetAssetsMasterList as GetAssetsMasterListCommand;
use App\Repositories\AssetRepository;
use Doctrine\ORM\EntityManager;

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
     * @return \Illuminate\Support\Collection
     * @throws \Exception
     */
    public function handle(GetAssetsMasterListCommand $command)
    {
        $requestingUser = $this->authenticate();

        // No command parameters are needed as we are just fetching all the assets owned by the authenticated User
        return $requestingUser->getAssets()->toArray();
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