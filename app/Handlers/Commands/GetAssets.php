<?php

namespace App\Handlers\Commands;

use App\Commands\GetAssets as GetAssetsCommand;
use App\Entities\Asset;
use App\Exceptions\ActionNotPermittedException;
use App\Policies\ComponentPolicy;
use App\Repositories\AssetRepository;

class GetAssets extends CommandHandler
{
    /** @var AssetRepository */
    protected $assetRepository;

    /**
     * GetAssets constructor.
     *
     * @param AssetRepository $assetRepository
     */
    public function __construct(AssetRepository $assetRepository)
    {
        $this->assetRepository = $assetRepository;
    }

    /**
     * Process the GetAssets command.
     *
     * @param GetAssetsCommand $command
     * @return array
     * @throws ActionNotPermittedException
     */
    public function handle(GetAssetsCommand $command)
    {
        $requestingUser = $this->authenticate();

        if ($requestingUser->cannot(ComponentPolicy::ACTION_LIST, new Asset())) {
            throw new ActionNotPermittedException("The authenticated User does not have permission to list Assets");
        }

        $assets = $this->assetRepository->findBy([Asset::ID => $command->getAssetIds()]);
        if (empty($assets)) {
            return [];
        }

        return $assets;
    }
}