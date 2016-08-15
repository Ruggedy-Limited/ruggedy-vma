<?php
​
namespace App\Transformers;
​
use App\Entities\Team;
use League\Fractal\TransformerAbstract;

class TeamTransformer extends TransformerAbstract
{
     /**
     * Transform a Team entity for the API
     *
     * @param Team $team
     * @return array
     */
    public function transform(Team $team)
    {
        return [
            'id'                   => $team->getId(),
            'name'                 => $team->getName(),
            'emailAddress'         => $team->getEmail(),
            'photo'                => $team->getPhotoUrl(),
            'twoFactorAuthEnabled' => $team->getUsesTwoFactorAuth(),
            'createdDate'          => $team->getCreatedAt(),
            'modifiedDate'         => $team->getUpdatedAt(),
        ];
    }
}