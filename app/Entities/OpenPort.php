<?php

namespace App\Entities;

use App\Contracts\SystemComponent;
use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entities\OpenPort
 *
 * @ORM\Entity(repositoryClass="App\Repositories\OpenPortRepository")
 * @ORM\HasLifecycleCallbacks
 */
class OpenPort extends Base\OpenPort implements SystemComponent
{
    /**
     * @ORM\ManyToOne(targetEntity="File", inversedBy="openPorts", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(name="`file_id`", referencedColumnName="`id`", nullable=false)
     */
    protected $file;

    /**
     * @return Base\User
     */
    function getUser()
    {
        return $this->getFile()->getUser();
    }

    /**
     * @return Base\Asset
     */
    function getParent()
    {
        return $this->getAsset();
    }
}