<?php

namespace App\Entities;

use App\Contracts\HasGetId;
use App\Contracts\HasOwnerUserEntity;
use App\Contracts\SystemComponent;
use Doctrine\ORM\Mapping as ORM;


/**
 * App\Entities\Asset
 *
 * @ORM\Entity(repositoryClass="App\Repositories\AssetRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Asset extends Base\Asset implements SystemComponent
{
    /**
     * Get the parent Entity of this Entity
     *
     * @return Base\Workspace
     */
    public function getParent()
    {
        return $this->getWorkspace();
    }
}