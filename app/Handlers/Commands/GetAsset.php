<?php

namespace App\Handlers\Commands;

use App\Commands\GetAsset as GetAssetCommand;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\AssetNotFoundException;
use App\Policies\ComponentPolicy;
use App\Repositories\AssetRepository;

class GetAsset extends CommandHandler
{
    /** @var AssetRepository */
    protected $assetRepository;

    /**
     * GetAsset constructor.
     *
     * @param AssetRepository $assetRepository
     */
    public function __construct(AssetRepository $assetRepository)
    {
        $this->assetRepository = $assetRepository;
    }

    /**
     * Process the GetAssetCommand
     *
     * @param GetAssetCommand $command
     * @return null|object
     * @throws ActionNotPermittedException
     * @throws AssetNotFoundException
     */
    public function handle(GetAssetCommand $command)
    {
        $requestingUser = $this->authenticate();

        $asset = $this->assetRepository->find($command->getId());
        if (empty($asset)) {
            throw new AssetNotFoundException("No Asset with the given ID was found");
        }

        if ($requestingUser->cannot(ComponentPolicy::ACTION_VIEW, $asset)) {
            throw new ActionNotPermittedException("Requesting User not permiited to view this Asset");
        }

        return $asset;
    }
}