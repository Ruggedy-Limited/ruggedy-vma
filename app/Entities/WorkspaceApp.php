<?php

namespace App\Entities;

use App\Contracts\SystemComponent;
use App\Entities\Base\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entities\WorkspaceApp
 *
 * @ORM\Entity(repositoryClass="App\Repositories\WorkspaceAppRepository")
 * @ORM\HasLifecycleCallbacks
 */
class WorkspaceApp extends Base\WorkspaceApp implements SystemComponent
{
    const DEFAULT_NAME        = 'Unnamed';
    const DEFAULT_DESCRIPTION = 'No description given. Please add a relevant description.';

    /**
     * @inheritdoc
     * @return User
     */
    public function getUser()
    {
        return $this->getWorkspace()->getUser();
    }

    /**
     * @inheritdoc
     * @param User $user
     * @return $this
     */
    public function setUser(User $user)
    {
        return $this;
    }

    /**
     * @inheritdoc
     * @return Base\Workspace
     */
    public function getParent()
    {
        return $this->getWorkspace();
    }
}