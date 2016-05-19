<?php

namespace App\Entities\Base;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * App\Entities\Base\Project
 *
 * @ORM\Entity(repositoryClass="App\Repositories\ProjectRepository")
 * @ORM\Table(name="`projects`", indexes={@ORM\Index(name="projects_user_id_foreign", columns={"`user_id`"})})
 */
class Project extends AbstractEntity
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
     * @ORM\Column(name="`deleted`", type="smallint", options={"unsigned":true})
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
     * @ORM\OneToMany(targetEntity="Workspace", mappedBy="project", cascade={"persist"})
     * @ORM\JoinColumn(name="`id`", referencedColumnName="`project_id`", nullable=false)
     */
    protected $workspaces;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="projects", cascade={"persist"})
     * @ORM\JoinColumn(name="`user_id`", referencedColumnName="`id`", nullable=false)
     */
    protected $user;

    public function __construct()
    {
        $this->workspaces = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entities\Base\Project
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
     * @return \App\Entities\Base\Project
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
     * @return \App\Entities\Base\Project
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
     * @param integer $deleted
     * @return \App\Entities\Base\Project
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get the value of deleted.
     *
     * @return integer
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set the value of created_at.
     *
     * @param \DateTime $created_at
     * @return \App\Entities\Base\Project
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
     * @return \App\Entities\Base\Project
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
     * Add Workspace entity to collection (one to many).
     *
     * @param \App\Entities\Base\Workspace $workspace
     * @return \App\Entities\Base\Project
     */
    public function addWorkspace(Workspace $workspace)
    {
        $this->workspaces[] = $workspace;

        return $this;
    }

    /**
     * Remove Workspace entity from collection (one to many).
     *
     * @param \App\Entities\Base\Workspace $workspace
     * @return \App\Entities\Base\Project
     */
    public function removeWorkspace(Workspace $workspace)
    {
        $this->workspaces->removeElement($workspace);

        return $this;
    }

    /**
     * Get Workspace entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getWorkspaces()
    {
        return $this->workspaces;
    }

    /**
     * Set User entity (many to one).
     *
     * @param \App\Entities\Base\User $user
     * @return \App\Entities\Base\Project
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

    public function __sleep()
    {
        return array('id', 'name', 'user_id', 'deleted', 'created_at', 'updated_at');
    }
}