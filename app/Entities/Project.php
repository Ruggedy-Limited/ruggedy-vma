<?php

namespace App\Entities;

use App\Contracts\SystemComponent;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * App\Entities\Project
 *
 * @ORM\Entity(repositoryClass="App\Repositories\ProjectRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Project extends Base\Project implements SystemComponent
{
    /**
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="projects", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(name="`user_id`", referencedColumnName="`id`", nullable=false)
     */
    protected $user;

    /**
     * Get the parent Entity of this Entity
     * 
     * @return Base\User
     */
    public function getParent()
    {
        return $this->getUser();
    }
}