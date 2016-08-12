<?php
​
namespace App\Transformers;
​
use App\Entities\User;
use League\Fractal\TransformerAbstract;
​
class ComponentPersmissionTransformer extends TransformerAbstract
{
     /**
     * Transform a ComponentPermission entity for the API
     *
     * @param ComponentPermission $component_permission
     * @return array
     */
    public function transform(ComponenetPermission $component_permission)
    {
        return [
            'id'                   => $component_permission->getId(),
            'name'                 => $component_permission->getName(),
            'emailAddress'         => $component_permission->getEmail(),
            'photo'                => $component_permission->getPhotoUrl(),
            'twoFactorAuthEnabled' => $component_permission->getUsesTwoFactorAuth(),
            'createdDate'          => $component_permission->getCreatedAt(),
            'modifiedDate'         => $component_permission->getUpdatedAt(),
        ]
    }
}