<?php
​
namespace App\Transformers;
​
use App\Entities\Announcement;
use League\Fractal\TransformerAbstract;
​

class AnnouncementTransformer extends TransformerAbstract
{
    /**
     * Transform a Announcent entity for the API
     *
     * @param Announcement $announcement
     * @return array
     */
    public function transform(Announcement $announcement)
    {
        return [
            'id'                   => $announcement->getId(),
            'name'                 => $announcement->getName(),
            'emailAddress'         => $announcement->getEmail(),
            'photo'                => $announcement->getPhotoUrl(),
            'twoFactorAuthEnabled' => $announcement->getUsesTwoFactorAuth(),
            'createdDate'          => $announcement->getCreatedAt(),
            'modifiedDate'         => $announcement->getUpdatedAt(),
        ];
    }
}