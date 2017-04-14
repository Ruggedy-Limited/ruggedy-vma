<?php

namespace App\Entities\Base;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * App\Entities\Base\Workspace
 *
 * @ORM\MappedSuperclass
 * @ORM\Table(name="`workspaces`", indexes={@ORM\Index(name="workspaces_fk_user", columns={"`user_id`"})})
 */
class Workspace extends AbstractEntity
{
    /** Table name constant */
    const TABLE_NAME = 'workspaces';

    /** Column name constants */
    const NAME          = 'name';
    const DESCRIPTION   = 'description';
    const USER_ID       = 'user_id';
    const DELETED       = 'deleted';
    const FOLDERS       = 'folders';
    const WORKSPACEAPPS = 'workspaceApps';
    const USER          = 'user';

    /**
     * @ORM\Id
     * @ORM\Column(name="`id`", type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="`name`", type="string", length=255)
     */
    protected $name;

    /**
     * @ORM\Column(name="`description`", type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(name="`user_id`", type="integer", options={"unsigned":true})
     */
    protected $user_id;

    /**
     * @ORM\Column(name="`deleted`", type="boolean", options={"unsigned":true})
     */
    protected $deleted;

    /**
     * @ORM\Column(name="`created_at`", type="datetime", nullable=true)
     */
    protected $created_at;

    /**
     * @ORM\Column(name="`updated_at`", type="datetime", nullable=true)
     */
    protected $updated_at;

    /**
     * @ORM\OneToMany(targetEntity="Folder", mappedBy="workspace", cascade={"persist"})
     * @ORM\JoinColumn(name="`id`", referencedColumnName="`workspace_id`", nullable=false)
     */
    protected $folders;

    /**
     * @ORM\OneToMany(targetEntity="WorkspaceApp", mappedBy="workspace", cascade={"persist"})
     * @ORM\JoinColumn(name="`id`", referencedColumnName="`workspace_id`", nullable=false, onDelete="CASCADE")
     */
    protected $workspaceApps;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="workspaces", cascade={"persist"})
     * @ORM\JoinColumn(name="`user_id`", referencedColumnName="`id`", nullable=false)
     */
    protected $user;

    public function __construct()
    {
        $this->folders = new ArrayCollection();
        $this->workspaceApps = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entities\Base\Workspace
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of name.
     *
     * @param string $name
     * @return \App\Entities\Base\Workspace
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of description.
     *
     * @param string $description
     * @return \App\Entities\Base\Workspace
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the value of description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the value of user_id.
     *
     * @param integer $user_id
     * @return \App\Entities\Base\Workspace
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;

        return $this;
    }

    /**
     * Get the value of user_id.
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Set the value of deleted.
     *
     * @param boolean $deleted
     * @return \App\Entities\Base\Workspace
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get the value of deleted.
     *
     * @return boolean
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set the value of created_at.
     *
     * @param \DateTime $created_at
     * @return \App\Entities\Base\Workspace
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * Get the value of created_at.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set the value of updated_at.
     *
     * @param \DateTime $updated_at
     * @return \App\Entities\Base\Workspace
     */
    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * Get the value of updated_at.
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Add Folder entity to collection (one to many).
     *
     * @param \App\Entities\Base\Folder $folder
     * @return \App\Entities\Base\Workspace
     */
    public function addFolder(Folder $folder)
    {
        $this->folders[] = $folder;

        return $this;
    }

    /**
     * Remove Folder entity from collection (one to many).
     *
     * @param \App\Entities\Base\Folder $folder
     * @return \App\Entities\Base\Workspace
     */
    public function removeFolder(Folder $folder)
    {
        $this->folders->removeElement($folder);

        return $this;
    }

    /**
     * Get Folder entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFolders()
    {
        return $this->folders;
    }

    /**
     * Add WorkspaceApp entity to collection (one to many).
     *
     * @param \App\Entities\Base\WorkspaceApp $workspaceApp
     * @return \App\Entities\Base\Workspace
     */
    public function addWorkspaceApp(WorkspaceApp $workspaceApp)
    {
        $this->workspaceApps[] = $workspaceApp;

        return $this;
    }

    /**
     * Remove WorkspaceApp entity from collection (one to many).
     *
     * @param \App\Entities\Base\WorkspaceApp $workspaceApp
     * @return \App\Entities\Base\Workspace
     */
    public function removeWorkspaceApp(WorkspaceApp $workspaceApp)
    {
        $this->workspaceApps->removeElement($workspaceApp);

        return $this;
    }

    /**
     * Get WorkspaceApp entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getWorkspaceApps()
    {
        return $this->workspaceApps;
    }

    /**
     * Set User entity (many to one).
     *
     * @param \App\Entities\Base\User $user
     * @return \App\Entities\Base\Workspace
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get User entity (many to one).
     *
     * @return \App\Entities\Base\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Get the display name for the entity
     *
     * @param bool $plural
     * @return string
     */
    public function getDisplayName(bool $plural = false): string
    {
        return $plural === false ? 'Workspace' : 'Workspaces';
    }

    public function __sleep()
    {
        return array('id', 'name', 'description', 'user_id', 'deleted', 'created_at', 'updated_at');
    }
}