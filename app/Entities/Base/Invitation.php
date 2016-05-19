<?php

namespace App\Entities\Base;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entities\Base\Invitation
 *
 * @ORM\Entity(repositoryClass="App\Repositories\InvitationRepository")
 * @ORM\Table(name="`invitations`", indexes={@ORM\Index(name="invitations_team_id_index", columns={"`team_id`"}), @ORM\Index(name="invitations_user_id_index", columns={"`user_id`"})}, uniqueConstraints={@ORM\UniqueConstraint(name="invitations_token_unique", columns={"`token`"})})
 */
class Invitation extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(name="`id`", type="string", length=255)
     */
    protected $id;

    /**
     * @ORM\Column(name="`team_id`", type="integer")
     */
    protected $team_id;

    /**
     * @ORM\Column(name="`user_id`", type="integer", nullable=true)
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

    public function __sleep()
    {
        return array('id', 'team_id', 'user_id', 'email', 'token', 'created_at', 'updated_at');
    }
}