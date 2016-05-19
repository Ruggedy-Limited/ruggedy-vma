<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entities\TeamSubscription
 *
 * @ORM\Entity(repositoryClass="App\Repositories\TeamSubscriptionRepository")
 * @ORM\HasLifecycleCallbacks
 */
class TeamSubscription extends Base\TeamSubscription
{
}