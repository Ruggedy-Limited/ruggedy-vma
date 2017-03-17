<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repositories\ApiTokenRepository")
 * @ORM\HasLifecycleCallbacks
 */
class ApiToken extends Base\ApiToken
{
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="apiTokens", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(name="`user_id`", referencedColumnName="`id`", nullable=false)
     */
    protected $user;
}