<?php

namespace App\Entities\Base;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entities\Base\ApiToken
 *
 * @ORM\MappedSuperclass
 * @ORM\Table(name="`api_tokens`", indexes={@ORM\Index(name="api_tokens_user_id_expires_at_index", columns={"`user_id`", "`expires_at`"})}, uniqueConstraints={@ORM\UniqueConstraint(name="api_tokens_token_unique", columns={"`token`"})})
 */
class ApiToken extends AbstractEntity
{
    /** Table name constant */
    const TABLE_NAME = 'api_tokens';

    /** Column name constants */
    const USER_ID      = 'user_id';
    const NAME         = 'name';
    const TOKEN        = 'token';
    const METADATA     = 'metadata';
    const TRANSIENT    = 'transient';
    const LAST_USED_AT = 'last_used_at';
    const EXPIRES_AT   = 'expires_at';
    const USER         = 'user';

    /**
     * @ORM\Id
     * @ORM\Column(name="`id`", type="string", length=255)
     */
    protected $id;

    /**
     * @ORM\Column(name="`user_id`", type="integer", options={"unsigned":true})
     */
    protected $user_id;

    /**
     * @ORM\Column(name="`name`", type="string", length=255)
     */
    protected $name;

    /**
     * @ORM\Column(name="`token`", type="string", length=100)
     */
    protected $token;

    /**
     * @ORM\Column(name="`metadata`", type="text")
     */
    protected $metadata;

    /**
     * @ORM\Column(name="`transient`", type="boolean")
     */
    protected $transient;

    /**
     * @ORM\Column(name="`last_used_at`", type="datetime", nullable=true)
     */
    protected $last_used_at;

    /**
     * @ORM\Column(name="`expires_at`", type="datetime", nullable=true)
     */
    protected $expires_at;

    /**
     * @ORM\Column(name="`created_at`", type="datetime", nullable=true)
     */
    protected $created_at;

    /**
     * @ORM\Column(name="`updated_at`", type="datetime", nullable=true)
     */
    protected $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="apiTokens", cascade={"persist"})
     * @ORM\JoinColumn(name="`user_id`", referencedColumnName="`id`", nullable=false)
     */
    protected $user;

    public function __construct()
    {
    }

    /**
     * Set the value of id.
     *
     * @param string $id
     * @return \App\Entities\Base\ApiToken
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
     * Set the value of user_id.
     *
     * @param integer $user_id
     * @return \App\Entities\Base\ApiToken
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
     * Set the value of name.
     *
     * @param string $name
     * @return \App\Entities\Base\ApiToken
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
     * Set the value of token.
     *
     * @param string $token
     * @return \App\Entities\Base\ApiToken
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
     * Set the value of metadata.
     *
     * @param string $metadata
     * @return \App\Entities\Base\ApiToken
     */
    public function setMetadata($metadata)
    {
        $this->metadata = $metadata;

        return $this;
    }

    /**
     * Get the value of metadata.
     *
     * @return string
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * Set the value of transient.
     *
     * @param boolean $transient
     * @return \App\Entities\Base\ApiToken
     */
    public function setTransient($transient)
    {
        $this->transient = $transient;

        return $this;
    }

    /**
     * Get the value of transient.
     *
     * @return boolean
     */
    public function getTransient()
    {
        return $this->transient;
    }

    /**
     * Set the value of last_used_at.
     *
     * @param \DateTime $last_used_at
     * @return \App\Entities\Base\ApiToken
     */
    public function setLastUsedAt($last_used_at)
    {
        $this->last_used_at = $last_used_at;

        return $this;
    }

    /**
     * Get the value of last_used_at.
     *
     * @return \DateTime
     */
    public function getLastUsedAt()
    {
        return $this->last_used_at;
    }

    /**
     * Set the value of expires_at.
     *
     * @param \DateTime $expires_at
     * @return \App\Entities\Base\ApiToken
     */
    public function setExpiresAt($expires_at)
    {
        $this->expires_at = $expires_at;

        return $this;
    }

    /**
     * Get the value of expires_at.
     *
     * @return \DateTime
     */
    public function getExpiresAt()
    {
        return $this->expires_at;
    }

    /**
     * Set the value of created_at.
     *
     * @param \DateTime $created_at
     * @return \App\Entities\Base\ApiToken
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
     * @return \App\Entities\Base\ApiToken
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
     * Set User entity (many to one).
     *
     * @param \App\Entities\Base\User $user
     * @return \App\Entities\Base\ApiToken
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
        return array('id', 'user_id', 'name', 'token', 'metadata', 'transient', 'last_used_at', 'expires_at', 'created_at', 'updated_at');
    }
}