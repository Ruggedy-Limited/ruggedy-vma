<?php
​
namespace App\Transformers;
​
use App\Entities\User;
use League\Fractal\TransformerAbstract;

class InvitationTransformer extends TransformerAbstract
{
	 /**
     * Transform a Invitaion entity for the API
     *
     * @param Invitaion $invitation
     * @return array
     */
    public function transform(Invitation $invitaion)
    {
        return [
        	'id'                   => $invitation->getId(),
            'name'                 => $invitation->getName(),
            'emailAddress'         => $invitation->getEmail(),
            'photo'                => $invitation->getPhotoUrl(),
            'twoFactorAuthEnabled' => $invitation->getUsesTwoFactorAuth(),
            'createdDate'          => $invitation->getCreatedAt(),
            'modifiedDate'         => $invitation->getUpdatedAt(),
        ]
    }
}