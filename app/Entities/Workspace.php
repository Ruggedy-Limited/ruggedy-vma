<?php

namespace App\Entities;

use App\Contracts\SystemComponent;
use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entities\Workspace
 *
 * @ORM\Entity(repositoryClass="App\Repositories\WorkspaceRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Workspace extends Base\Workspace implements SystemComponent
{
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="workspaces", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(name="`user_id`", referencedColumnName="`id`", nullable=false)
     */
    protected $user;

    /**
     * Get the parent Entity of this Entity
     *
     * @return Base\Project
     */
    public function getParent()
    {
        return $this->getProject();
    }
}