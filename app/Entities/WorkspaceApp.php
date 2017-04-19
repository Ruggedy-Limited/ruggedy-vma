<?php

namespace App\Entities;

use App\Contracts\SystemComponent;
use App\Entities\Base\File;
use App\Entities\Base\User;
use App\Entities\Base\Vulnerability;
use Doctrine\ORM\Mapping as ORM;
use Illuminate\Support\Collection;

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
     * @ORM\OneToMany(targetEntity="File", mappedBy="workspaceApp", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="`id`", referencedColumnName="`workspace_apps_id`", nullable=false)
     */
    protected $files;

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
        return $this->workspace;
    }

    /**
     * @param File $file
     * @return Base\WorkspaceApp
     */
    public function addFile(File $file)
    {
        $file->setWorkspaceApp($this);
        return parent::addFile($file);
    }

    /**
     * Get if this is a Ruggedy App or not
     *
     * @return bool
     */
    public function isRuggedyApp(): bool
    {
        return $this->scannerApp->getName() === ScannerApp::SCANNER_RUGGEDY;
    }
}