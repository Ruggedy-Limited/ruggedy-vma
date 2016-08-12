<?php
​
namespace App\Transformers;
​
use App\Entities\User;
use League\Fractal\TransformerAbstract;

class NotificationTransformer extends TransformerAbstract
{
     /**
     * Transform a Notification entity for the API
     *
     * @param Notification $notification
     * @return array
     */
    public function transform(Notification $notification)
    {
        return [
            'id'                   => $notification->getId(),
            'name'                 => $notification->getName(),
            'emailAddress'         => $notification->getEmail(),
            'photo'                => $notification->getPhotoUrl(),
            'twoFactorAuthEnabled' => $notification->getUsesTwoFactorAuth(),
            'createdDate'          => $notification->getCreatedAt(),
            'modifiedDate'         => $notification->getUpdatedAt(),
        ]
    }
}