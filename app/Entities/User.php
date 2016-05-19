<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * App\Entities\User
 *
 * @ORM\Entity(repositoryClass="App\Repositories\UserRepository")
 * @ORM\HasLifecycleCallbacks
 */
class User extends Base\User
{
}