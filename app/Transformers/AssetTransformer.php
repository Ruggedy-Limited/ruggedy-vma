<?php

namespace App\Transformers;

use App\Entities\Asset;
use League\Fractal\TransformerAbstract;

class AssetTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'audits',
        'files',
        'openPorts',
        'softwareInformation',
        'vulnerabilities',
    ];

     /**
     * Transform a Asset entity for the API
     *
     * @param Asset $asset
     * @return array
     */
    public function transform(Asset $asset)
    {
        return [
            'id'           => $asset->getId(),
            'name'         => $asset->getName(),
            'cpe'          => $asset->getCpe(),
            'ipAddress'    => $asset->getIpAddressV4(),
            'ipAddressV6'  => $asset->getIpAddressV6(),
            'hostname'     => $asset->getHostname(),
            'macAddress'   => $asset->getMacAddress(),
            'os'           => $asset->getVendor(),
            'osVersion'    => $asset->getOsVersion(),
            'userId'       => $asset->getUser()->getId(),
            'workspaceId'  => $asset->getWorkspace()->getId(),
            'isSuppressed' => $asset->getSuppressed(),
            'isDeleted'    => $asset->getDeleted(),
            'createdDate'  => $asset->getCreatedAt()->format(env('APP_DATE_FORMAT')),
            'modifiedDate' => $asset->getUpdatedAt()->format(env('APP_DATE_FORMAT')),
        ];
    }

    /**
     * Optional include for Audits
     *
     * @param Asset $asset
     * @return \League\Fractal\Resource\Collection
     */
    public function includeAudits(Asset $asset)
    {
        return $this->collection($asset->getAudits(), new AuditTransformer());
    }

    /**
     * Optional include for Open Ports
     *
     * @param Asset $asset
     * @return \League\Fractal\Resource\Collection
     */
    public function includeOpenPorts(Asset $asset)
    {
        return $this->collection($asset->getOpenPorts(), new OpenPortTransformer());
    }

    /**
     * Optional include for Software Information
     *
     * @param Asset $asset
     * @return \League\Fractal\Resource\Collection
     */
    public function includeSoftwareInformation(Asset $asset)
    {
        return $this->collection($asset->getRelatedSoftwareInformation(), new SoftwareInformationTransformer());
    }

    /**
     * Optional include for Files
     *
     * @param Asset $asset
     * @return \League\Fractal\Resource\Collection
     */
    public function includeFiles(Asset $asset)
    {
        return $this->collection($asset->getFiles(), new FileTransformer());
    }

    /**
     * Optional include for Vulnerabilities
     *
     * @param Asset $asset
     * @return \League\Fractal\Resource\Collection
     */
    public function includeVulnerabilities(Asset $asset)
    {
        return $this->collection($asset->getVulnerabilities(), new VulnerabilityTransformer());
    }
}