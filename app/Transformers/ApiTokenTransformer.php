<?php

namespace App\Transformers;

use App\Entities\ApiToken;
use League\Fractal\TransformerAbstract;

class ApiTokenTransformer extends TransformerAbstract
{
     /**
     * Transform a ApiToken entity for the API
     *
     * @param ApiToken $apiToken
     * @return array
     */
    public function transform(ApiToken $apiToken)
    {
        return [
            'id'             => $apiToken->getId(),
            'userId'         => $apiToken->getUserId(),
            'name'           => $apiToken->getName(),
            'tokenString'    => $apiToken->getToken(),
            'metaDataString' => $apiToken->getMetadata(),
            'isTransient'    => boolval($apiToken->getTransient()),
            'lastUsed'       => $apiToken->getLastUsedAt(),
            'expiryDate'     => $apiToken->getExpiresAt(),
            'createdDate'    => $apiToken->getCreatedAt(),
            'modifiedDate'   => $apiToken->getUpdatedAt(),
        ];
    }
}