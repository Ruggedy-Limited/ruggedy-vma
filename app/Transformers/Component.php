<?php
​
namespace App\Transformers;
​
use App\Entities\User;
use League\Fractal\TransformerAbstract;
​
class ComponentTransformer extends TransformerAbstract
{
     /**
     * Transform a Component entity for the API
     *
     * @param Component $component
     * @return array
     */
    public function transform(Component $component)
    {
        return [
            'id'                   => $component->getId(),
            'name'                 => $component->getName(),
            'emailAddress'         => $component->getEmail(),
            'photo'                => $component->getPhotoUrl(),
            'twoFactorAuthEnabled' => $component->getUsesTwoFactorAuth(),
            'createdDate'          => $component->getCreatedAt(),
            'modifiedDate'         => $component->getUpdatedAt(),
        ]
    }
}