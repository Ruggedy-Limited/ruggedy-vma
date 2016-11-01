<?php

namespace App\Transformers;

use App\Entities\TeamSubscription;
use League\Fractal\TransformerAbstract;

class TeamSubscriptionTransformer extends TransformerAbstract
{
     /**
     * Transform a TeamSubscription entity for the API
     *
     * @param TeamSubscription $teamSubscription
     * @return array
     */
    public function transform(TeamSubscription $teamSubscription)
    {
        return [
            'id'           => $teamSubscription->getId(),
            'name'         => $teamSubscription->getName(),
            'team'         => $teamSubscription->getTeam(),
            'stripeId'     => $teamSubscription->getStripeId(),
            'stripePlan'   => $teamSubscription->getStripePlan(),
            'quantity'     => $teamSubscription->getQuantity(),
            'trialEndDate' => $teamSubscription->getTrialEndsAt(),
            'endDate'      => $teamSubscription->getEndsAt(),
            'createdDate'  => $teamSubscription->getCreatedAt(),
            'modifiedDate' => $teamSubscription->getUpdatedAt(),
        ];
    }
}