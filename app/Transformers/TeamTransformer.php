<?php

namespace App\Transformers;

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
            'id'                => $team->getId(),
            'name'              => $team->getName(),
            'photo'             => $team->getPhotoUrl(),
            'owner'             => $team->getUser(),
            'members'           => $team->getUsers(),
            'stripeId'          => $team->getStripeId(),
            'billingPlan'       => $team->getCurrentBillingPlan(),
            'vatId'             => $team->getVatId(),
            'trialEndDate'      => $team->getTrialEndsAt(),
            'invitations'       => $team->getInvitations(),
            'invoices'          => $team->getInvoices(),
            'teamSubscriptions' => $team->getTeamSubscriptions(),
            'permissions'       => $team->getComponentPermissions(),
            'createdDate'       => $team->getCreatedAt(),
            'modifiedDate'      => $team->getUpdatedAt(),
        ];
    }
}