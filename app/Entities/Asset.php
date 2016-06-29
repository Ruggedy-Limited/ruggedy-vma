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
    /** Regular expressions used for validating the relevant Asset data fields */
    const REGEX_CPE         = 'cpe:(?\d)(?\.\d):[aho](?::(?:[a-zA-Z0-9!"#$%&\'()*+,\\\-_.\/;<=>?@\[\]^`{|}~]|\\:)+){10}$';
    const REGEX_MAC_ADDRESS = '^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$';
    
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