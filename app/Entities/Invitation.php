<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entities\Invitation
 *
 * @ORM\Entity(repositoryClass="App\Repositories\InvitationRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Invitation extends Base\Invitation
{
}