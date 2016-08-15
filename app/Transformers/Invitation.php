<?php
​
namespace App\Transformers;
​
use App\Entities\Invitaion;
use League\Fractal\TransformerAbstract;

class InvitationTransformer extends TransformerAbstract
{
	 /**
     * Transform a Invitation entity for the API
     *
     * @param Invitation $invitation
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
        ];
    }
}