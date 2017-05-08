<?php

namespace App\Entities\Base;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entities\Base\Folder
 *
 * @ORM\MappedSuperclass
 * @ORM\Table(name="`folders`", indexes={
 *     @ORM\Index(name="folder_workspace_fk_idx", columns={"`workspace_id`"}),
 *     @ORM\Index(name="folder_user_fk_idx", columns={"`user_id`"})
 * })
 */
class Folder extends AbstractEntity
{
    /** Table name constant */
    const TABLE_NAME = 'folders';

    /** Column name constants */
    const NAME                   = 'name';
    const DESCRIPTION            = 'description';
    const WORKSPACE_ID           = 'workspace_id';
    const USER_ID                = 'user_id';
    const WORKSPACE              = 'workspace';
    const USER                   = 'user';

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
     * @ORM\Column(name="`workspace_id`", type="integer", options={"unsigned":true})
     */
    protected $workspace_id;

    /**
     * @ORM\Column(name="`user_id`", type="integer", options={"unsigned":true})
     */
    protected $user_id;

    /**
     * @ORM\Column(name="`created_at`", type="datetime")
     */
    protected $created_at;

    /**
     * @ORM\Column(name="`updated_at`", type="datetime")
     */
    protected $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity="Workspace", inversedBy="folders", cascade={"persist"})
     * @ORM\JoinColumn(name="`workspace_id`", referencedColumnName="`id`", nullable=false)
     */
    protected $workspace;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="folders", cascade={"persist"})
     * @ORM\JoinColumn(name="`user_id`", referencedColumnName="`id`", nullable=false)
     */
    protected $user;

    public function __construct()
    {
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entities\Base\Folder
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
     * @return \App\Entities\Base\Folder
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
     * @return \App\Entities\Base\Folder
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
     * Set the value of workspace_id.
     *
     * @param integer $workspace_id
     * @return \App\Entities\Base\Folder
     */
    public function setWorkspaceId($workspace_id)
    {
        $this->workspace_id = $workspace_id;

        return $this;
    }

    /**
     * Get the value of workspace_id.
     *
     * @return integer
     */
    public function getWorkspaceId()
    {
        return $this->workspace_id;
    }

    /**
     * Set the value of user_id.
     *
     * @param integer $user_id
     * @return \App\Entities\Base\Folder
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
     * Set the value of created_at.
     *
     * @param \DateTime $created_at
     * @return \App\Entities\Base\Folder
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
     * @return \App\Entities\Base\Folder
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
     * Set Workspace entity (many to one).
     *
     * @param \App\Entities\Base\Workspace $workspace
     * @return \App\Entities\Base\Folder
     */
    public function setWorkspace(Workspace $workspace = null)
    {
        $this->workspace = $workspace;

        return $this;
    }

    /**
     * Get Workspace entity (many to one).
     *
     * @return \App\Entities\Base\Workspace
     */
    public function getWorkspace()
    {
        return $this->workspace;
    }

    /**
     * Set User entity (many to one).
     *
     * @param \App\Entities\Base\User $user
     * @return \App\Entities\Base\Folder
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
        return $plural === false ? 'Folder' : 'Folders';
    }

    public function __sleep()
    {
        return array('id', 'name', 'description', 'workspace_id', 'file_id', 'user_id', 'created_at', 'updated_at');
    }
}