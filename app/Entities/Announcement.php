<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repositories\AnnouncementRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Announcement extends Base\Announcement
{
}