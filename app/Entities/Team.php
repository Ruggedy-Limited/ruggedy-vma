<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entities\Team
 *
 * @ORM\Entity(repositoryClass="App\Repositories\TeamRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Team extends Base\Team
{
}