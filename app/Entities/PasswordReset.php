<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entities\PasswordReset
 *
 * @ORM\Entity(repositoryClass="App\Repositories\PasswordResetRepository")
 * @ORM\HasLifecycleCallbacks
 */
class PasswordReset extends Base\PasswordReset
{
}