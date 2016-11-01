<?php

namespace App\Transformers;

use App\Entities\ComponentPermission;
use League\Fractal\TransformerAbstract;

class ComponentPermissionTransformer extends TransformerAbstract
{
     /**
     * Transform a ComponentPermission entity for the API
     *
     * @param ComponentPermission $componentPermission
     * @return array
     */
    public function transform(ComponentPermission $componentPermission)
    {
        return [
            'id'                   => $componentPermission->getId(),
            'component'            => $componentPermission->getComponent(),
            'instanceId'           => $componentPermission->getInstanceId(),
            'permission'           => $componentPermission->getPermission(),
            'user'                 => $componentPermission->getUserRelatedByUserId(),
            'grantedBy'            => $componentPermission->getUserRelatedByGrantedBy(),
            'isGrantedToTeam'      => !empty($componentPermission->getTeamId()),
            'team'                 => $componentPermission->getTeam(),
            'createdDate'          => $componentPermission->getCreatedAt(),
            'modifiedDate'         => $componentPermission->getUpdatedAt(),
        ];
    }
}