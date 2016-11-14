<?php

namespace App\Transformers;

use App\Entities\OpenPort;
use League\Fractal\TransformerAbstract;

class OpenPortTransformer extends TransformerAbstract
{
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
            'asset'                   => $openPort->getAsset(),
            'createdDate'             => $openPort->getCreatedAt(),
            'modifiedDate'            => $openPort->getUpdatedAt(),
        ];
    }
}