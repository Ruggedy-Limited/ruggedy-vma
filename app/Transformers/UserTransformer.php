<?php

namespace App\Transformers;

use App\Entities\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    /**
     * Transform a User entity for the API
     *
     * @param User $user
     * @return array
     */
    public function transform(User $user)
    {
        return [
            'id'                   => $user->getId(),
            'name'                 => $user->getName(),
            'emailAddress'         => $user->getEmail(),
            'countryCode'          => $user->getCountryCode(),
            'phoneNo'              => $user->getPhone(),
            'activeTeam'           => $user->getTeam(),
            'teams'                => $user->getTeams(),
            'workspaces'           => $user->getWorkspaces(),
            'photo'                => $user->getPhotoUrl(),
            'twoFactorAuthEnabled' => $user->getUsesTwoFactorAuth(),
            'createdDate'          => $user->getCreatedAt()->format(env('APP_DATE_FORMAT')),
            'modifiedDate'         => $user->getUpdatedAt()->format(env('APP_DATE_FORMAT')),
        ];
    }
}