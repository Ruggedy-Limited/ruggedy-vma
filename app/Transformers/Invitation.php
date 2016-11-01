<?php

namespace App\Transformers;

use App\Entities\Invitation;
use League\Fractal\TransformerAbstract;

class InvitationTransformer extends TransformerAbstract
{
	 /**
     * Transform a Invitation entity for the API
     *
     * @param Invitation $invitation
     * @return array
     */
    public function transform(Invitation $invitation)
    {
        return [
            'id'                   => $invitation->getId(),
            'emailAddress'         => $invitation->getEmail(),
            'team'                 => $invitation->getTeam(),
            'user'                 => $invitation->getUser(),
            'token'                => $invitation->getToken(),
            'isForTeam'            => !empty($invitation->getTeam()),
            'createdDate'          => $invitation->getCreatedAt(),
            'modifiedDate'         => $invitation->getUpdatedAt(),
        ];
    }
}