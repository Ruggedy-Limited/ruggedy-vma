<?php

namespace App\Transformers;

use App\Entities\ApiToken;
use League\Fractal\TransformerAbstract;

class ApiTokenTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'user',
    ];

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
            'name'           => $apiToken->getName(),
            'tokenString'    => $apiToken->getToken(),
            'metaDataString' => $apiToken->getMetadata(),
            'isTransient'    => boolval($apiToken->getTransient()),
            'lastUsed'       => $apiToken->getLastUsedAt()->format(env('APP_DATE_FORMAT')),
            'expiryDate'     => $apiToken->getExpiresAt()->format(env('APP_DATE_FORMAT')),
            'createdDate'    => $apiToken->getCreatedAt()->format(env('APP_DATE_FORMAT')),
            'modifiedDate'   => $apiToken->getUpdatedAt()->format(env('APP_DATE_FORMAT')),
        ];
    }

    /**
     * Optional include for the related User entity
     *
     * @param ApiToken $apiToken
     * @return \League\Fractal\Resource\Item
     */
    public function includeUser(ApiToken $apiToken)
    {
        return $this->item($apiToken->getUser(), new UserTransformer());
    }
}