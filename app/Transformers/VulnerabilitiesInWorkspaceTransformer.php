<?php

namespace App\Transformers;

use App\Entities\Asset;
use App\Entities\Vulnerability;
use App\Entities\Workspace;
use League\Fractal\TransformerAbstract;

class VulnerabilitiesInWorkspaceTransformer extends TransformerAbstract
{
    /**
     * Transform a Collection of Assets into a Collection of Vulnerabilities grouped by Asset for the API
     *
     * @param Workspace $workspace
     * @return array
     */
    public function transform(Workspace $workspace)
    {
        $vulnerabilitiesByAsset = $workspace->getAssets()->map(function ($asset) {
            /** @var Asset $asset */
            // Get an array of transformed Vulnerabilities
            $vulnerabilities = $asset->getVulnerabilities()->map(function ($vulnerability) {
                /** @var Vulnerability $vulnerability */
                return fractal()->item($vulnerability, new VulnerabilityTransformer());
            })->toArray();

            return [
                'assetId'         => $asset->getId(),
                'assetName'       => $asset->getName(),
                'vulnerabilities' => $vulnerabilities,
            ];
        })->toArray();

        return [
            'id'              => $workspace->getId(),
            'name'            => $workspace->getName(),
            'vulnerabilities' => $vulnerabilitiesByAsset,
        ];
    }
}