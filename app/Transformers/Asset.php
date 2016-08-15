<?php
​
namespace App\Transformers;
​
use App\Entities\Assetet;
use League\Fractal\TransformerAbstract;
​
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
            'emailAddress'         => $asset->getEmail(),
            'photo'                => $asset->getPhotoUrl(),
            'twoFactorAuthEnabled' => $asset->getUsesTwoFactorAuth(),
            'createdDate'          => $asset->getCreatedAt(),
            'modifiedDate'         => $asset->getUpdatedAt(),
        ];
    }
}