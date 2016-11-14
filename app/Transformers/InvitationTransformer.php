<?php

namespace App\Transformers;

use Laravel\Spark\Invitation;
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
            'id'           => $invitation->id,
            'emailAddress' => $invitation->email,
            'userId'       => $invitation->user_id,
            'token'        => $invitation->token,
            'isForTeam'    => !empty($invitation->team),
            'createdDate'  => $invitation->created_at,
            'modifiedDate' => $invitation->updated_at,
        ];
    }
}