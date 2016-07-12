<?php

namespace App\Entities\Base;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * App\Entities\Base\Workspace
 *
 * @ORM\MappedSuperclass
 * @ORM\Table(name="`workspaces`", indexes={@ORM\Index(name="workspaces_fk_user", columns={"`user_id`"}), @ORM\Index(name="workspaces_fk_project", columns={"`project_id`"})})
 */
class Workspace extends AbstractEntity
{
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
     * @ORM\Column(name="`user_id`", type="integer", options={"unsigned":true})
     */
    protected $user_id;

    /**
     * @ORM\Column(name="`project_id`", type="integer", options={"unsigned":true})
     */
    protected $project_id;

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
     * @ORM\OneToMany(targetEntity="Asset", mappedBy="workspace", cascade={"persist"})
     * @ORM\JoinColumn(name="`id`", referencedColumnName="`workspace_id`", nullable=false)
     */
    protected $assets;

    /**
     * @ORM\OneToMany(targetEntity="File", mappedBy="workspace", cascade={"persist"})
     * @ORM\JoinColumn(name="`id`", referencedColumnName="`workspace_id`", nullable=false)
     */
    protected $files;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="workspaces", cascade={"persist"})
     * @ORM\JoinColumn(name="`user_id`", referencedColumnName="`id`", nullable=false)
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="workspaces", cascade={"persist"})
     * @ORM\JoinColumn(name="`project_id`", referencedColumnName="`id`", nullable=false)
     */
    protected $project;

    public function __construct()
    {
        $this->assets = new ArrayCollection();
        $this->files = new ArrayCollection();
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
     * Set the value of project_id.
     *
     * @param integer $project_id
     * @return \App\Entities\Base\Workspace
     */
    public function setProjectId($project_id)
    {
        $this->project_id = $project_id;

        return $this;
    }

    /**
     * Get the value of project_id.
     *
     * @return integer
     */
    public function getProjectId()
    {
        return $this->project_id;
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
     * Add Asset entity to collection (one to many).
     *
     * @param \App\Entities\Base\Asset $asset
     * @return \App\Entities\Base\Workspace
     */
    public function addAsset(Asset $asset)
    {
        $this->assets[] = $asset;

        return $this;
    }

    /**
     * Remove Asset entity from collection (one to many).
     *
     * @param \App\Entities\Base\Asset $asset
     * @return \App\Entities\Base\Workspace
     */
    public function removeAsset(Asset $asset)
    {
        $this->assets->removeElement($asset);

        return $this;
    }

    /**
     * Get Asset entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAssets()
    {
        return $this->assets;
    }

    /**
     * Add File entity to collection (one to many).
     *
     * @param \App\Entities\Base\File $file
     * @return \App\Entities\Base\Workspace
     */
    public function addFile(File $file)
    {
        $this->files[] = $file;

        return $this;
    }

    /**
     * Remove File entity from collection (one to many).
     *
     * @param \App\Entities\Base\File $file
     * @return \App\Entities\Base\Workspace
     */
    public function removeFile(File $file)
    {
        $this->files->removeElement($file);

        return $this;
    }

    /**
     * Get File entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFiles()
    {
        return $this->files;
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
     * Set Project entity (many to one).
     *
     * @param \App\Entities\Base\Project $project
     * @return \App\Entities\Base\Workspace
     */
    public function setProject(Project $project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get Project entity (many to one).
     *
     * @return \App\Entities\Base\Project
     */
    public function getProject()
    {
        return $this->project;
    }

    public function __sleep()
    {
        return array('id', 'name', 'user_id', 'project_id', 'deleted', 'created_at', 'updated_at');
    }
}