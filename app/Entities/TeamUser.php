<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entities\TeamUser
 *
 * @ORM\Entity(repositoryClass="App\Repositories\TeamUserRepository")
 * @ORM\HasLifecycleCallbacks
 */
class TeamUser extends Base\TeamUser
{
}