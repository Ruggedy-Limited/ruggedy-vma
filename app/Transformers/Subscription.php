<?php
​
namespace App\Transformers;
​
use App\Entities\Subscription;
use League\Fractal\TransformerAbstract;

class SubscriptionTransformer extends TransformerAbstract
{
     /**
     * Transform a Subscription entity for the API
     *
     * @param Subscription $subscription
     * @return array
     */
    public function transform(Subscription $subscription)
    {
        return [
            'id'                   => $subscription->getId(),
            'name'                 => $subscription->getName(),
            'emailAddress'         => $subscription->getEmail(),
            'photo'                => $subscription->getPhotoUrl(),
            'twoFactorAuthEnabled' => $subscription->getUsesTwoFactorAuth(),
            'createdDate'          => $subscription->getCreatedAt(),
            'modifiedDate'         => $subscription->getUpdatedAt(),
        ];
    }
}