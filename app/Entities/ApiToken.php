<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repositories\ApiTokenRepository")
 * @ORM\HasLifecycleCallbacks
 */
class ApiToken extends Base\ApiToken
{
}