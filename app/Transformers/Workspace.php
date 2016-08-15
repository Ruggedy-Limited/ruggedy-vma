<?php
​
namespace App\Transformers;
​
use App\Entities\workspace;
use League\Fractal\TransformerAbstract;

class WorkspaceTransformer extends TransformerAbstract
{
     /**
     * Transform a Workspace entity for the API
     *
     * @param Workspace $workspace
     * @return array
     */
    public function transform(Workspace $workspace)
    {
        return [
            'id'                   => $workspace->getId(),
            'name'                 => $workspace->getName(),
            'emailAddress'         => $workspace->getEmail(),
            'photo'                => $workspace->getPhotoUrl(),
            'twoFactorAuthEnabled' => $workspace->getUsesTwoFactorAuth(),
            'createdDate'          => $workspace->getCreatedAt(),
            'modifiedDate'         => $workspace->getUpdatedAt(),
        ];
    }
}