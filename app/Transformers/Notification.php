<?php

namespace App\Transformers;

use App\Entities\Notification;
use League\Fractal\TransformerAbstract;

class NotificationTransformer extends TransformerAbstract
{
     /**
     * Transform a Notification entity for the API
     *
     * @param Notification $notification
     * @return array
     */
    public function transform(Notification $notification)
    {
        return [
            'id'                   => $notification->getId(),
            'user'                 => $notification->getUserRelatedByUserId(),
            'createdBy'            => $notification->getUserRelatedByCreatedBy(),
            'icon'                 => $notification->getIcon(),
            'content'              => $notification->getBody(),
            'action'               => $notification->getActionText(),
            'actionUrl'            => $notification->getActionUrl(),
            'isRead'               => !empty($notification->getRead()),
            'createdDate'          => $notification->getCreatedAt(),
            'modifiedDate'         => $notification->getUpdatedAt(),
        ];
    }
}