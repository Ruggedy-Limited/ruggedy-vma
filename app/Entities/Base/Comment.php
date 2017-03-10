<?php

namespace App\Entities\Base;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entities\Base\Comment
 *
 * @ORM\MappedSuperclass
 * @ORM\Table(name="`comments`", indexes={@ORM\Index(name="comments_user_fk_idx", columns={"`user_id`"}), @ORM\Index(name="comments_vulnerability_fk_idx", columns={"`vulnerability_id`"}), @ORM\Index(name="comments_file_fk_idx", columns={"`file_id`"})})
 */
class Comment extends AbstractEntity
{
    /** Table name constant */
    const TABLE_NAME = 'comments';

    /** Column name constants */
    const CONTENT          = 'content';
    const STATUS           = 'status';
    const USER_ID          = 'user_id';
    const FILE_ID          = 'file_id';
    const VULNERABILITY_ID = 'vulnerability_id';
    const USER             = 'user';
    const FILE             = 'file';
    const VULNERABILITY    = 'vulnerability';

    /**
     * @ORM\Id
     * @ORM\Column(name="`id`", type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="`content`", type="text")
     */
    protected $content;

    /**
     * @ORM\Column(name="`status`", type="boolean", options={"unsigned":true})
     */
    protected $status;

    /**
     * @ORM\Column(name="`user_id`", type="integer", options={"unsigned":true})
     */
    protected $user_id;

    /**
     * @ORM\Column(name="`file_id`", type="integer", options={"unsigned":true})
     */
    protected $file_id;

    /**
     * @ORM\Column(name="`vulnerability_id`", type="integer", nullable=true, options={"unsigned":true})
     */
    protected $vulnerability_id;

    /**
     * @ORM\Column(name="`created_at`", type="datetime")
     */
    protected $created_at;

    /**
     * @ORM\Column(name="`updated_at`", type="datetime")
     */
    protected $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="comments", cascade={"persist"})
     * @ORM\JoinColumn(name="`user_id`", referencedColumnName="`id`", nullable=false)
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="File", inversedBy="comments", cascade={"persist"})
     * @ORM\JoinColumn(name="`file_id`", referencedColumnName="`id`", nullable=false, onDelete="CASCADE")
     */
    protected $file;

    /**
     * @ORM\ManyToOne(targetEntity="Vulnerability", inversedBy="comments", cascade={"persist"})
     * @ORM\JoinColumn(name="`vulnerability_id`", referencedColumnName="`id`", onDelete="CASCADE")
     */
    protected $vulnerability;

    public function __construct()
    {
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entities\Base\Comment
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
     * Set the value of content.
     *
     * @param string $content
     * @return \App\Entities\Base\Comment
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get the value of content.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set the value of status.
     *
     * @param boolean $status
     * @return \App\Entities\Base\Comment
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get the value of status.
     *
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the value of user_id.
     *
     * @param integer $user_id
     * @return \App\Entities\Base\Comment
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
     * Set the value of file_id.
     *
     * @param integer $file_id
     * @return \App\Entities\Base\Comment
     */
    public function setFileId($file_id)
    {
        $this->file_id = $file_id;

        return $this;
    }

    /**
     * Get the value of file_id.
     *
     * @return integer
     */
    public function getFileId()
    {
        return $this->file_id;
    }

    /**
     * Set the value of vulnerability_id.
     *
     * @param integer $vulnerability_id
     * @return \App\Entities\Base\Comment
     */
    public function setVulnerabilityId($vulnerability_id)
    {
        $this->vulnerability_id = $vulnerability_id;

        return $this;
    }

    /**
     * Get the value of vulnerability_id.
     *
     * @return integer
     */
    public function getVulnerabilityId()
    {
        return $this->vulnerability_id;
    }

    /**
     * Set the value of created_at.
     *
     * @param \DateTime $created_at
     * @return \App\Entities\Base\Comment
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
     * @return \App\Entities\Base\Comment
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
     * @return \App\Entities\Base\Comment
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
     * Set File entity (many to one).
     *
     * @param \App\Entities\Base\File $file
     * @return \App\Entities\Base\Comment
     */
    public function setFile(File $file = null)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get File entity (many to one).
     *
     * @return \App\Entities\Base\File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set Vulnerability entity (many to one).
     *
     * @param \App\Entities\Base\Vulnerability $vulnerability
     * @return \App\Entities\Base\Comment
     */
    public function setVulnerability(Vulnerability $vulnerability = null)
    {
        $this->vulnerability = $vulnerability;

        return $this;
    }

    /**
     * Get Vulnerability entity (many to one).
     *
     * @return \App\Entities\Base\Vulnerability
     */
    public function getVulnerability()
    {
        return $this->vulnerability;
    }

    public function __sleep()
    {
        return array('id', 'content', 'status', 'user_id', 'file_id', 'vulnerability_id', 'created_at', 'updated_at');
    }
}