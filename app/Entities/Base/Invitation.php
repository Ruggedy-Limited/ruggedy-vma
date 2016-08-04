<?php

namespace App\Entities\Base;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entities\Base\Invitation
 *
 * @ORM\MappedSuperclass
 * @ORM\Table(name="`invitations`", indexes={@ORM\Index(name="invitations_team_id_index", columns={"`team_id`"}), @ORM\Index(name="invitations_user_id_index", columns={"`user_id`"})}, uniqueConstraints={@ORM\UniqueConstraint(name="invitations_token_unique", columns={"`token`"})})
 */
class Invitation extends AbstractEntity
{
    /** Table name constant */
    const TABLE_NAME = 'invitations';

    /** Column name constants */
    const TEAM_ID    = 'team_id';
    const USER_ID    = 'user_id';
    const EMAIL      = 'email';
    const TOKEN      = 'token';

    /**
     * @ORM\Id
     * @ORM\Column(name="`id`", type="string", length=255)
     */
    protected $id;

    /**
     * @ORM\Column(name="`team_id`", type="integer", options={"unsigned":true})
     */
    protected $team_id;

    /**
     * @ORM\Column(name="`user_id`", type="integer", nullable=true, options={"unsigned":true})
     */
    protected $user_id;

    /**
     * @ORM\Column(name="`email`", type="string", length=255)
     */
    protected $email;

    /**
     * @ORM\Column(name="`token`", type="string", length=40)
     */
    protected $token;

    /**
     * @ORM\Column(name="`created_at`", type="datetime", nullable=true)
     */
    protected $created_at;

    /**
     * @ORM\Column(name="`updated_at`", type="datetime", nullable=true)
     */
    protected $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity="Team", inversedBy="invitations", cascade={"persist"})
     * @ORM\JoinColumn(name="`team_id`", referencedColumnName="`id`", nullable=false)
     */
    protected $team;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="invitations", cascade={"persist"})
     * @ORM\JoinColumn(name="`user_id`", referencedColumnName="`id`")
     */
    protected $user;

    public function __construct()
    {
    }

    /**
     * Set the value of id.
     *
     * @param string $id
     * @return \App\Entities\Base\Invitation
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of team_id.
     *
     * @param integer $team_id
     * @return \App\Entities\Base\Invitation
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
     * Set the value of user_id.
     *
     * @param integer $user_id
     * @return \App\Entities\Base\Invitation
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
     * Set the value of email.
     *
     * @param string $email
     * @return \App\Entities\Base\Invitation
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of token.
     *
     * @param string $token
     * @return \App\Entities\Base\Invitation
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get the value of token.
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set the value of created_at.
     *
     * @param \DateTime $created_at
     * @return \App\Entities\Base\Invitation
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
     * @return \App\Entities\Base\Invitation
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
     * Set Team entity (many to one).
     *
     * @param \App\Entities\Base\Team $team
     * @return \App\Entities\Base\Invitation
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
     * Set User entity (many to one).
     *
     * @param \App\Entities\Base\User $user
     * @return \App\Entities\Base\Invitation
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
        return array('id', 'team_id', 'user_id', 'email', 'token', 'created_at', 'updated_at');
    }
}