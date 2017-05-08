<?php

namespace App\Handlers\Commands;

use App\Commands\GetAsset as GetAssetCommand;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\AssetNotFoundException;
use App\Policies\ComponentPolicy;
use App\Repositories\AssetRepository;
use App\Repositories\VulnerabilityRepository;

class GetAsset extends CommandHandler
{
    /** @var AssetRepository */
    protected $assetRepository;

    /** @var VulnerabilityRepository */
    protected $vulnerabilityRepository;

    /**
     * GetAsset constructor.
     *
     * @param AssetRepository $assetRepository
     */
    public function __construct(AssetRepository $assetRepository, VulnerabilityRepository $vulnerabilityRepository)
    {
        $this->assetRepository = $assetRepository;
        $this->vulnerabilityRepository = $vulnerabilityRepository;
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
            throw new ActionNotPermittedException("Requesting User not permitted to view this Asset");
        }

        $vulnerabilities = $this->vulnerabilityRepository->findByAssetQuery($command->getId());
        if (!empty($vulnerabilities)) {
            $vulnerabilities->getCollection()->transform(function ($result) {
                return current($result);   
            });
        }

        return collect([
            'asset' => $asset,
            'vulnerabilities' => $vulnerabilities,
        ]);
    }
}