<?php

namespace App\Transformers;

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
            'id'           => $subscription->getId(),
            'name'         => $subscription->getName(),
            'user'         => $subscription->getUser(),
            'stripeId'     => $subscription->getStripeId(),
            'stripePlan'   => $subscription->getStripePlan(),
            'quantity'     => $subscription->getQuantity(),
            'trialEndDate' => $subscription->getTrialEndsAt()->format(env('APP_DATE_FORMAT')),
            'endDate'      => $subscription->getEndsAt()->format(env('APP_DATE_FORMAT')),
            'createdDate'  => $subscription->getCreatedAt()->format(env('APP_DATE_FORMAT')),
            'modifiedDate' => $subscription->getUpdatedAt()->format(env('APP_DATE_FORMAT')),
        ];
    }
}