<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entities\Notification
 *
 * @ORM\Entity(repositoryClass="App\Repositories\NotificationRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Notification extends Base\Notification
{
}