<?php
​
namespace App\Transformers;
​
use App\Entities\User;
use League\Fractal\TransformerAbstract;
​
class ApiTokenTransformer extends TransformerAbstract
{
     /**
     * Transform a ApiToken entity for the API
     *
     * @param ApiToken $api_token
     * @return array
     */
    public function transform(ApiToken $api_token)
    {
        return [
            'id'                   => $api_token->getId(),
            'name'                 => $api_token->getName(),
            'emailAddress'         => $api_token->getEmail(),
            'photo'                => $api_token->getPhotoUrl(),
            'twoFactorAuthEnabled' => $api_token->getUsesTwoFactorAuth(),
            'createdDate'          => $api_token->getCreatedAt(),
            'modifiedDate'         => $api_token->getUpdatedAt(),
        ]
    }
}