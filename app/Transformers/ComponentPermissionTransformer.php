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
        // Allow for NULL team relations
        $teamId = null;
        if (!empty($componentPermission->getTeam())) {
            $teamId = $componentPermission->getTeam()->getId();
        }

        // Allow for NULL user relations
        $userId = null;
        if (!empty($componentPermission->getUserRelatedByUserId())) {
            $userId = $componentPermission->getUserRelatedByUserId()->getId();
        }

        return [
            'id'              => $componentPermission->getId(),
            'componentName'   => $componentPermission->getComponent()->getName(),
            'instanceId'      => $componentPermission->getInstanceId(),
            'permission'      => $componentPermission->getPermission(),
            'userId'          => $userId,
            'grantedByUserId' => $componentPermission->getUserRelatedByGrantedBy()->getId(),
            'isGrantedToTeam' => !empty($componentPermission->getTeamId()),
            'teamId'          =>  $teamId,
            'createdDate'     => $componentPermission->getCreatedAt(),
            'modifiedDate'    => $componentPermission->getUpdatedAt(),
        ];
    }
}