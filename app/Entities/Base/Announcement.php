<?php

namespace App\Entities\Base;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entities\Base\Announcement
 *
 * @ORM\MappedSuperclass
 * @ORM\Table(name="`announcements`", indexes={@ORM\Index(name="announcements_fk_user_idx", columns={"`user_id`"})})
 */
class Announcement extends AbstractEntity
{
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
     * @ORM\Column(name="`body`", type="text")
     */
    protected $body;

    /**
     * @ORM\Column(name="`action_text`", type="string", length=255, nullable=true)
     */
    protected $action_text;

    /**
     * @ORM\Column(name="`action_url`", type="text", nullable=true)
     */
    protected $action_url;

    /**
     * @ORM\Column(name="`created_at`", type="datetime", nullable=true)
     */
    protected $created_at;

    /**
     * @ORM\Column(name="`updated_at`", type="datetime", nullable=true)
     */
    protected $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="announcements", cascade={"persist"})
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
     * @return \App\Entities\Base\Announcement
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
     * @return \App\Entities\Base\Announcement
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
     * Set the value of body.
     *
     * @param string $body
     * @return \App\Entities\Base\Announcement
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get the value of body.
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set the value of action_text.
     *
     * @param string $action_text
     * @return \App\Entities\Base\Announcement
     */
    public function setActionText($action_text)
    {
        $this->action_text = $action_text;

        return $this;
    }

    /**
     * Get the value of action_text.
     *
     * @return string
     */
    public function getActionText()
    {
        return $this->action_text;
    }

    /**
     * Set the value of action_url.
     *
     * @param string $action_url
     * @return \App\Entities\Base\Announcement
     */
    public function setActionUrl($action_url)
    {
        $this->action_url = $action_url;

        return $this;
    }

    /**
     * Get the value of action_url.
     *
     * @return string
     */
    public function getActionUrl()
    {
        return $this->action_url;
    }

    /**
     * Set the value of created_at.
     *
     * @param \DateTime $created_at
     * @return \App\Entities\Base\Announcement
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
     * @return \App\Entities\Base\Announcement
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
     * @return \App\Entities\Base\Announcement
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
        return array('id', 'user_id', 'body', 'action_text', 'action_url', 'created_at', 'updated_at');
    }
}