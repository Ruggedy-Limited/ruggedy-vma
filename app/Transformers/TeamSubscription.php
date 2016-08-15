<?php
​
namespace App\Transformers;
​
use App\Entities\TeamSubscription;
use League\Fractal\TransformerAbstract;

class TeamSubscriptionTransformer extends TransformerAbstract
{
     /**
     * Transform a TeamSubscription entity for the API
     *
     * @param TeamSubscription $team_subscription
     * @return array
     */
    public function transform(TeamSubscription $teamSubscription)
    {
        return [
            'id'                   => $team_subscription->getId(),
            'name'                 => $team_subscription->getName(),
            'emailAddress'         => $team_subscription->getEmail(),
            'photo'                => $team_subscription->getPhotoUrl(),
            'twoFactorAuthEnabled' => $team_subscription->getUsesTwoFactorAuth(),
            'createdDate'          => $team_subscription->getCreatedAt(),
            'modifiedDate'         => $team_subscription->getUpdatedAt(),
        ];
    }
}