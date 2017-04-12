<?php

namespace App\Entities\Base;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * App\Entities\Base\File
 *
 * @ORM\MappedSuperclass
 * @ORM\Table(name="`files`", indexes={@ORM\Index(name="files_user_fk_idx", columns={"`user_id`"}), @ORM\Index(name="files_workspace_apps_fk_idx", columns={"`workspace_app_id`"})})
 */
class File extends AbstractEntity
{
    /** Table name constant */
    const TABLE_NAME = 'files';

    /** Column name constants */
    const NAME                   = 'name';
    const DESCRIPTION            = 'description';
    const PATH                   = 'path';
    const FORMAT                 = 'format';
    const SIZE                   = 'size';
    const USER_ID                = 'user_id';
    const WORKSPACE_APP_ID       = 'workspace_app_id';
    const PROCESSED              = 'processed';
    const DELETED                = 'deleted';
    const ASSETS                 = 'assets';
    const VULNERABILITIES        = 'vulnerabilities';
    const JIRAISSUES             = 'jiraIssues';
    const USER                   = 'user';
    const WORKSPACEAPP           = 'workspaceApp';

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
     * @ORM\Column(name="`path`", type="string", length=255)
     */
    protected $path;

    /**
     * @ORM\Column(name="`format`", type="string")
     */
    protected $format;

    /**
     * @ORM\Column(name="`size`", type="integer", options={"unsigned":true})
     */
    protected $size;

    /**
     * @ORM\Column(name="`user_id`", type="integer", options={"unsigned":true})
     */
    protected $user_id;

    /**
     * @ORM\Column(name="`workspace_app_id`", type="integer", options={"unsigned":true})
     */
    protected $workspace_app_id;

    /**
     * @ORM\Column(name="`processed`", type="boolean", options={"unsigned":true})
     */
    protected $processed;

    /**
     * @ORM\Column(name="`deleted`", type="boolean", options={"unsigned":true})
     */
    protected $deleted;

    /**
     * @ORM\Column(name="`created_at`", type="datetime")
     */
    protected $created_at;

    /**
     * @ORM\Column(name="`updated_at`", type="datetime")
     */
    protected $updated_at;

    /**
     * @ORM\OneToMany(targetEntity="Asset", mappedBy="file", cascade={"persist"})
     * @ORM\JoinColumn(name="`id`", referencedColumnName="`file_id`", nullable=false, onDelete="CASCADE")
     */
    protected $assets;

    /**
     * @ORM\OneToMany(targetEntity="Vulnerability", mappedBy="file", cascade={"persist"})
     * @ORM\JoinColumn(name="`id`", referencedColumnName="`file_id`", nullable=false, onDelete="CASCADE")
     */
    protected $vulnerabilities;

    /**
     * @ORM\OneToMany(targetEntity="JiraIssue", mappedBy="file", cascade={"persist"})
     * @ORM\JoinColumn(name="`id`", referencedColumnName="`file_id`", nullable=false, onDelete="CASCADE")
     */
    protected $jiraIssues;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="files", cascade={"persist"})
     * @ORM\JoinColumn(name="`user_id`", referencedColumnName="`id`", nullable=false)
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="WorkspaceApp", inversedBy="files", cascade={"persist"})
     * @ORM\JoinColumn(name="`workspace_app_id`", referencedColumnName="`id`", nullable=false, onDelete="CASCADE")
     */
    protected $workspaceApp;

    public function __construct()
    {
        $this->assets = new ArrayCollection();
        $this->vulnerabilities = new ArrayCollection();
        $this->jiraIssues = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entities\Base\File
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
     * @return \App\Entities\Base\File
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
     * @return \App\Entities\Base\File
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
     * Set the value of path.
     *
     * @param string $path
     * @return \App\Entities\Base\File
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get the value of path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set the value of format.
     *
     * @param string $format
     * @return \App\Entities\Base\File
     */
    public function setFormat($format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Get the value of format.
     *
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Set the value of size.
     *
     * @param integer $size
     * @return \App\Entities\Base\File
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get the value of size.
     *
     * @return integer
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set the value of user_id.
     *
     * @param integer $user_id
     * @return \App\Entities\Base\File
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
     * Set the value of workspace_app_id.
     *
     * @param integer $workspace_app_id
     * @return \App\Entities\Base\File
     */
    public function setWorkspaceAppId($workspace_app_id)
    {
        $this->workspace_app_id = $workspace_app_id;

        return $this;
    }

    /**
     * Get the value of workspace_app_id.
     *
     * @return integer
     */
    public function getWorkspaceAppId()
    {
        return $this->workspace_app_id;
    }

    /**
     * Set the value of processed.
     *
     * @param boolean $processed
     * @return \App\Entities\Base\File
     */
    public function setProcessed($processed)
    {
        $this->processed = $processed;

        return $this;
    }

    /**
     * Get the value of processed.
     *
     * @return boolean
     */
    public function getProcessed()
    {
        return $this->processed;
    }

    /**
     * Set the value of deleted.
     *
     * @param boolean $deleted
     * @return \App\Entities\Base\File
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
     * @return \App\Entities\Base\File
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
     * @return \App\Entities\Base\File
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
     * @return \App\Entities\Base\File
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
     * @return \App\Entities\Base\File
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
     * Add Vulnerability entity to collection (one to many).
     *
     * @param \App\Entities\Base\Vulnerability $vulnerability
     * @return \App\Entities\Base\File
     */
    public function addVulnerability(Vulnerability $vulnerability)
    {
        $this->vulnerabilities[] = $vulnerability->setFile($this);

        return $this;
    }

    /**
     * Remove Vulnerability entity from collection (one to many).
     *
     * @param \App\Entities\Base\Vulnerability $vulnerability
     * @return \App\Entities\Base\File
     */
    public function removeVulnerability(Vulnerability $vulnerability)
    {
        $this->vulnerabilities->removeElement($vulnerability);

        return $this;
    }

    /**
     * Get Vulnerability entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVulnerabilities()
    {
        return $this->vulnerabilities;
    }

    /**
     * Add JiraIssue entity to collection (one to many).
     *
     * @param \App\Entities\Base\JiraIssue $jiraIssue
     * @return \App\Entities\Base\File
     */
    public function addJiraIssue(JiraIssue $jiraIssue)
    {
        $this->jiraIssues[] = $jiraIssue;

        return $this;
    }

    /**
     * Remove JiraIssue entity from collection (one to many).
     *
     * @param \App\Entities\Base\JiraIssue $jiraIssue
     * @return \App\Entities\Base\File
     */
    public function removeJiraIssue(JiraIssue $jiraIssue)
    {
        $this->jiraIssues->removeElement($jiraIssue);

        return $this;
    }

    /**
     * Get JiraIssue entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getJiraIssues()
    {
        return $this->jiraIssues;
    }

    /**
     * Set User entity (many to one).
     *
     * @param \App\Entities\Base\User $user
     * @return \App\Entities\Base\File
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
     * Set WorkspaceApp entity (many to one).
     *
     * @param \App\Entities\Base\WorkspaceApp $workspaceApp
     * @return \App\Entities\Base\File
     */
    public function setWorkspaceApp(WorkspaceApp $workspaceApp = null)
    {
        $this->workspaceApp = $workspaceApp;

        return $this;
    }

    /**
     * Get WorkspaceApp entity (many to one).
     *
     * @return \App\Entities\Base\WorkspaceApp
     */
    public function getWorkspaceApp()
    {
        return $this->workspaceApp;
    }

    public function __sleep()
    {
        return array('id', 'name', 'description', 'path', 'format', 'size', 'user_id', 'workspace_app_id', 'processed', 'deleted', 'created_at', 'updated_at');
    }
}