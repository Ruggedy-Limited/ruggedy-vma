<?php

namespace App\Transformers;

use App\Entities\Announcement;
use League\Fractal\TransformerAbstract;

class AnnouncementTransformer extends TransformerAbstract
{
    /**
     * Transform a Announcement entity for the API
     *
     * @param Announcement $announcement
     * @return array
     */
    public function transform(Announcement $announcement)
    {
        return [
            'id'           => $announcement->getId(),
            'userId'       => $announcement->getUserId(),
            'content'      => $announcement->getBody(),
            'action'       => $announcement->getActionText(),
            'actionUrl'    => $announcement->getActionUrl(),
            'createdDate'  => $announcement->getCreatedAt()->format(env('APP_DATE_FORMAT')),
            'modifiedDate' => $announcement->getUpdatedAt()->format(env('APP_DATE_FORMAT')),
        ];
    }
}