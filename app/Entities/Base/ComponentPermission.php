<?php

namespace App\Entities\Base;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entities\Base\ComponentPermission
 *
 * @ORM\MappedSuperclass
 * @ORM\Table(name="`component_permissions`", indexes={@ORM\Index(name="component_permissions_component_fk_idx", columns={"`component_id`"}), @ORM\Index(name="component_permissions_user_fk_idx", columns={"`user_id`"}), @ORM\Index(name="component_permissions_user_granted_fk_idx", columns={"`granted_by`"}), @ORM\Index(name="component_permissions_team_fk_idx", columns={"`team_id`"})})
 */
class ComponentPermission extends AbstractEntity
{
    /** Table name constant */
    const TABLE_NAME = 'component_permissions';

    /** Column name constants */
    const COMPONENT_ID           = 'component_id';
    const INSTANCE_ID            = 'instance_id';
    const PERMISSION             = 'permission';
    const USER_ID                = 'user_id';
    const TEAM_ID                = 'team_id';
    const GRANTED_BY             = 'granted_by';
    const COMPONENT              = 'component';
    const USERRELATEDBYUSERID    = 'userRelatedByUserId';
    const TEAM                   = 'team';
    const USERRELATEDBYGRANTEDBY = 'userRelatedByGrantedBy';

    /**
     * @ORM\Id
     * @ORM\Column(name="`id`", type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="`component_id`", type="integer", options={"unsigned":true})
     */
    protected $component_id;

    /**
     * The id of the instance of the relevant component
     *
     * @ORM\Column(name="`instance_id`", type="integer", options={"unsigned":true})
     */
    protected $instance_id;

    /**
     * @ORM\Column(name="`permission`", type="string")
     */
    protected $permission;

    /**
     * @ORM\Column(name="`user_id`", type="integer", nullable=true, options={"unsigned":true})
     */
    protected $user_id;

    /**
     * @ORM\Column(name="`team_id`", type="integer", nullable=true, options={"unsigned":true})
     */
    protected $team_id;

    /**
     * @ORM\Column(name="`granted_by`", type="integer", options={"unsigned":true})
     */
    protected $granted_by;

    /**
     * @ORM\Column(name="`created_at`", type="datetime")
     */
    protected $created_at;

    /**
     * @ORM\Column(name="`updated_at`", type="datetime")
     */
    protected $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity="Component", inversedBy="componentPermissions", cascade={"persist"})
     * @ORM\JoinColumn(name="`component_id`", referencedColumnName="`id`", nullable=false)
     */
    protected $component;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="componentPermissionRelatedByUserIds", cascade={"persist"})
     * @ORM\JoinColumn(name="`user_id`", referencedColumnName="`id`")
     */
    protected $userRelatedByUserId;

    /**
     * @ORM\ManyToOne(targetEntity="Team", inversedBy="componentPermissions", cascade={"persist"})
     * @ORM\JoinColumn(name="`team_id`", referencedColumnName="`id`")
     */
    protected $team;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="componentPermissionRelatedByGrantedBies", cascade={"persist"})
     * @ORM\JoinColumn(name="`granted_by`", referencedColumnName="`id`", nullable=false)
     */
    protected $userRelatedByGrantedBy;

    public function __construct()
    {
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entities\Base\ComponentPermission
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
     * Set the value of component_id.
     *
     * @param integer $component_id
     * @return \App\Entities\Base\ComponentPermission
     */
    public function setComponentId($component_id)
    {
        $this->component_id = $component_id;

        return $this;
    }

    /**
     * Get the value of component_id.
     *
     * @return integer
     */
    public function getComponentId()
    {
        return $this->component_id;
    }

    /**
     * Set the value of instance_id.
     *
     * @param integer $instance_id
     * @return \App\Entities\Base\ComponentPermission
     */
    public function setInstanceId($instance_id)
    {
        $this->instance_id = $instance_id;

        return $this;
    }

    /**
     * Get the value of instance_id.
     *
     * @return integer
     */
    public function getInstanceId()
    {
        return $this->instance_id;
    }

    /**
     * Set the value of permission.
     *
     * @param string $permission
     * @return \App\Entities\Base\ComponentPermission
     */
    public function setPermission($permission)
    {
        $this->permission = $permission;

        return $this;
    }

    /**
     * Get the value of permission.
     *
     * @return string
     */
    public function getPermission()
    {
        return $this->permission;
    }

    /**
     * Set the value of user_id.
     *
     * @param integer $user_id
     * @return \App\Entities\Base\ComponentPermission
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
     * Set the value of team_id.
     *
     * @param integer $team_id
     * @return \App\Entities\Base\ComponentPermission
     */
    public function setTeamId($team_id)
    {
        $this->team_id = $team_id;

        return $this;
    }

    /**
     * Get the value of team_id.
     *
     * @return integer
     */
    public function getTeamId()
    {
        return $this->team_id;
    }

    /**
     * Set the value of granted_by.
     *
     * @param integer $granted_by
     * @return \App\Entities\Base\ComponentPermission
     */
    public function setGrantedBy($granted_by)
    {
        $this->granted_by = $granted_by;

        return $this;
    }

    /**
     * Get the value of granted_by.
     *
     * @return integer
     */
    public function getGrantedBy()
    {
        return $this->granted_by;
    }

    /**
     * Set the value of created_at.
     *
     * @param \DateTime $created_at
     * @return \App\Entities\Base\ComponentPermission
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
     * @return \App\Entities\Base\ComponentPermission
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
     * Set Component entity (many to one).
     *
     * @param \App\Entities\Base\Component $component
     * @return \App\Entities\Base\ComponentPermission
     */
    public function setComponent(Component $component = null)
    {
        $this->component = $component;

        return $this;
    }

    /**
     * Get Component entity (many to one).
     *
     * @return \App\Entities\Base\Component
     */
    public function getComponent()
    {
        return $this->component;
    }

    /**
     * Set User entity related by `user_id` (many to one).
     *
     * @param \App\Entities\Base\User $user
     * @return \App\Entities\Base\ComponentPermission
     */
    public function setUserRelatedByUserId(User $user = null)
    {
        $this->userRelatedByUserId = $user;

        return $this;
    }

    /**
     * Get User entity related by `user_id` (many to one).
     *
     * @return \App\Entities\Base\User
     */
    public function getUserRelatedByUserId()
    {
        return $this->userRelatedByUserId;
    }

    /**
     * Set Team entity (many to one).
     *
     * @param \App\Entities\Base\Team $team
     * @return \App\Entities\Base\ComponentPermission
     */
    public function setTeam(Team $team = null)
    {
        $this->team = $team;

        return $this;
    }

    /**
     * Get Team entity (many to one).
     *
     * @return \App\Entities\Base\Team
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * Set User entity related by `granted_by` (many to one).
     *
     * @param \App\Entities\Base\User $user
     * @return \App\Entities\Base\ComponentPermission
     */
    public function setUserRelatedByGrantedBy(User $user = null)
    {
        $this->userRelatedByGrantedBy = $user;

        return $this;
    }

    /**
     * Get User entity related by `granted_by` (many to one).
     *
     * @return \App\Entities\Base\User
     */
    public function getUserRelatedByGrantedBy()
    {
        return $this->userRelatedByGrantedBy;
    }

    public function __sleep()
    {
        return array('id', 'component_id', 'instance_id', 'permission', 'user_id', 'team_id', 'granted_by', 'created_at', 'updated_at');
    }
}