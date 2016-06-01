<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entities\Subscription
 *
 * @ORM\Entity(repositoryClass="App\Repositories\SubscriptionRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Subscription extends Base\Subscription
{
}