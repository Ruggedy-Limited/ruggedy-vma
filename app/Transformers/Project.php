<?php
​
namespace App\Transformers;
​
use App\Entities\User;
use League\Fractal\TransformerAbstract;

class ProjectTransformer extends TransformerAbstract
{
     /**
     * Transform a Project entity for the API
     *
     * @param Project $project
     * @return array
     */
    public function transform(Project $project)
    {
        return [
            'id'                   => $project->getId(),
            'name'                 => $project->getName(),
            'emailAddress'         => $project->getEmail(),
            'photo'                => $project->getPhotoUrl(),
            'twoFactorAuthEnabled' => $project->getUsesTwoFactorAuth(),
            'createdDate'          => $project->getCreatedAt(),
            'modifiedDate'         => $project->getUpdatedAt(),
        ]
    }
}