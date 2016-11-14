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
     * Override the parent method to set the inverse side of the relationship in the given Asset entity
     *
     * @param Base\Asset $asset
     * @return Base\Workspace
     */
    public function addAsset(Base\Asset $asset)
    {
        $asset->setWorkspace($this);
        return parent::addAsset($asset);
    }

    /**
     * Get the parent Entity of this Entity
     *
     * @return Base\User
     */
    public function getParent()
    {
        return $this->user;
    }
}