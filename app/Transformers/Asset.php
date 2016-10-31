<?php

namespace App\Transformers;

use App\Entities\Asset;
use League\Fractal\TransformerAbstract;

class AssetTransformer extends TransformerAbstract
{
     /**
     * Transform a Asset entity for the API
     *
     * @param Asset $asset
     * @return array
     */
    public function transform(Asset $asset)
    {
        return [
            'id'                   => $asset->getId(),
            'name'                 => $asset->getName(),
            'cpe'                  => $asset->getCpe(),
            'ipAddress'            => $asset->getIpAddressV4(),
            'hostname'             => $asset->getHostname(),
            'os'                   => $asset->getVendor(),
            'osVersion'            => $asset->getOsVersion(),
            'isSuppressed'         => $asset->getSuppressed(),
            'isDeleted'            => $asset->getDeleted(),
            'createdDate'          => $asset->getCreatedAt(),
            'modifiedDate'         => $asset->getUpdatedAt(),
            'userId'               => $asset->getUserId(),
            'workspaceId'          => $asset->getWorkspaceId(),
            'openPorts'            => $asset->getOpenPorts(),
            'softwareInformation'  => $asset->getRelatedSoftwareInformation(),
            'vulnerabilities'      => $asset->getVulnerabilities(),
            'files'                => $asset->getFiles(),
        ];
    }
}