<?php

namespace App\Transformers;

use App\Entities\OpenPort;
use League\Fractal\TransformerAbstract;

class OpenPortTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'asset',
    ];

    /**
     * Transform a OpenPort entity for the API
     *
     * @param OpenPort $openPort
     * @return array
     */
    public function transform(OpenPort $openPort)
    {
        return [
            'id'                      => $openPort->getId(),
            'portNumber'              => $openPort->getNumber(),
            'protocol'                => $openPort->getProtocol(),
            'serviceName'             => $openPort->getServiceName(),
            'serviceProduct'          => $openPort->getServiceProduct(),
            'serviceExtraInformation' => $openPort->getServiceExtraInfo(),
            'serviceFingerprint'      => $openPort->getServiceFingerPrint(),
            'serviceBanner'           => $openPort->getServiceBanner(),
            'serviceMessage'          => $openPort->getServiceMessage(),
            'createdDate'             => $openPort->getCreatedAt()->format(env('APP_DATE_FORMAT')),
            'modifiedDate'            => $openPort->getUpdatedAt()->format(env('APP_DATE_FORMAT')),
        ];
    }

    /**
     * Optional include for the related Asset
     *
     * @param OpenPort $openPort
     * @return \League\Fractal\Resource\Item
     */
    public function includeAsset(OpenPort $openPort)
    {
        return $this->item($openPort->getAsset(), new AssetTransformer());
    }
}