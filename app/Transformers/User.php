<?php
​
namespace App\Transformers;
​
use App\Entities\User;
use League\Fractal\TransformerAbstract;
​
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
            'photo'                => $user->getPhotoUrl(),
            'twoFactorAuthEnabled' => $user->getUsesTwoFactorAuth(),
            'createdDate'          => $user->getCreatedAt(),
            'modifiedDate'         => $user->getUpdatedAt(),
        ];
    }
}