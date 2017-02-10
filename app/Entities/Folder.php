<?php

namespace App\Entities;

use App\Contracts\SystemComponent;
use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entities\Folder
 *
 * @ORM\Entity(repositoryClass="App\Repositories\FolderRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Folder extends Base\Folder implements SystemComponent
{
    /**
     * @inheritdoc
     * @return Base\Workspace
     */
    public function getParent()
    {
        return $this->getWorkspace();
    }
}