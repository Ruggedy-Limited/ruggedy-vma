<?php

namespace App\Entities;

use App\Contracts\SystemComponent;
use App\Entities\Base\File;
use App\Entities\Base\User;
use Doctrine\Common\Collections\ArrayCollection;
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
     * @ORM\OneToMany(targetEntity="File", mappedBy="workspaceApp", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="`id`", referencedColumnName="`workspace_apps_id`", nullable=false)
     */
    protected $files;

    /**
     * @ORM\ManyToMany(targetEntity="Vulnerability", inversedBy="workspace_apps", cascade={"persist"}, fetch="EXTRA_LAZY")
     * @ORM\JoinTable(name="workspace_apps_vulnerabilities",
     *     joinColumns={@ORM\JoinColumn(name="workspace_app_id", referencedColumnName="id", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="vulnerability_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    protected $vulnerabilities;

    /**
     * WorkspaceApp constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->vulnerabilities = new ArrayCollection();
    }

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
     * @param Vulnerability $vulnerability
     * @return $this
     */
    public function addVulnerability(Vulnerability $vulnerability)
    {
        if ($this->vulnerabilities->contains($vulnerability)) {
            return $this;
        }

        $vulnerability->addWorkspaceApp($this);

        $vulnerabilityKey = $vulnerability->getId() ?? $vulnerability->getHash();
        $this->vulnerabilities[$vulnerabilityKey] = $vulnerability;

        return $this;
    }

    /**
     * @param Vulnerability $vulnerability
     * @return $this
     */
    public function removeVulnerability(Vulnerability $vulnerability)
    {
        $vulnerability->removeWorkspaceApp($this);
        $this->vulnerabilities->removeElement($vulnerability);

        return $this;
    }
}