<?php

namespace App\Transformers;

use App\Entities\SoftwareInformation;
use League\Fractal\TransformerAbstract;

class SoftwareInformationTransformer extends TransformerAbstract
{
    /**
     * Transform a SoftwareInformation entity for the API
     *
     * @param SoftwareInformation $softwareInformation
     * @return array
     */
    public function transform(SoftwareInformation $softwareInformation)
    {
        return [
            'id'           => $softwareInformation->getId(),
            'name'         => $softwareInformation->getName(),
            'version'      => $softwareInformation->getVersion(),
            'vendor'       => $softwareInformation->getVendor(),
            'createdDate'  => $softwareInformation->getCreatedAt()->format(env('APP_DATE_FORMAT')),
            'modifiedDate' => $softwareInformation->getUpdatedAt()->format(env('APP_DATE_FORMAT')),
        ];
    }
}