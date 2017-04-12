<?php

namespace App\Entities\Base;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * App\Entities\Base\WorkspaceApp
 *
 * @ORM\MappedSuperclass
 * @ORM\Table(name="`workspace_apps`", indexes={@ORM\Index(name="workspace_apps_scanner_app_fk_idx", columns={"`scanner_app_id`"}), @ORM\Index(name="workspace_apps_workspace_fk_idx", columns={"`workspace_id`"})})
 */
class WorkspaceApp extends AbstractEntity
{
    /** Table name constant */
    const TABLE_NAME = 'workspace_apps';

    /** Column name constants */
    const NAME            = 'name';
    const DESCRIPTION     = 'description';
    const SCANNER_APP_ID  = 'scanner_app_id';
    const WORKSPACE_ID    = 'workspace_id';
    const FILES           = 'files';
    const SCANNERAPP      = 'scannerApp';
    const WORKSPACE       = 'workspace';

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
     * @ORM\Column(name="`scanner_app_id`", type="integer", options={"unsigned":true})
     */
    protected $scanner_app_id;

    /**
     * @ORM\Column(name="`workspace_id`", type="integer", options={"unsigned":true})
     */
    protected $workspace_id;

    /**
     * @ORM\Column(name="`created_at`", type="datetime")
     */
    protected $created_at;

    /**
     * @ORM\Column(name="`updated_at`", type="datetime")
     */
    protected $updated_at;

    /**
     * @ORM\OneToMany(targetEntity="File", mappedBy="workspaceApp", cascade={"persist"})
     * @ORM\JoinColumn(name="`id`", referencedColumnName="`workspace_app_id`", nullable=false, onDelete="CASCADE")
     */
    protected $files;

    /**
     * @ORM\ManyToOne(targetEntity="ScannerApp", inversedBy="workspaceApps", cascade={"persist"})
     * @ORM\JoinColumn(name="`scanner_app_id`", referencedColumnName="`id`", nullable=false)
     */
    protected $scannerApp;

    /**
     * @ORM\ManyToOne(targetEntity="Workspace", inversedBy="workspaceApps", cascade={"persist"})
     * @ORM\JoinColumn(name="`workspace_id`", referencedColumnName="`id`", nullable=false, onDelete="CASCADE")
     */
    protected $workspace;

    public function __construct()
    {
        $this->files = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entities\Base\WorkspaceApp
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
     * @return \App\Entities\Base\WorkspaceApp
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
     * @return \App\Entities\Base\WorkspaceApp
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
     * Set the value of scanner_app_id.
     *
     * @param integer $scanner_app_id
     * @return \App\Entities\Base\WorkspaceApp
     */
    public function setScannerAppId($scanner_app_id)
    {
        $this->scanner_app_id = $scanner_app_id;

        return $this;
    }

    /**
     * Get the value of scanner_app_id.
     *
     * @return integer
     */
    public function getScannerAppId()
    {
        return $this->scanner_app_id;
    }

    /**
     * Set the value of workspace_id.
     *
     * @param integer $workspace_id
     * @return \App\Entities\Base\WorkspaceApp
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
     * Set the value of created_at.
     *
     * @param \DateTime $created_at
     * @return \App\Entities\Base\WorkspaceApp
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
     * @return \App\Entities\Base\WorkspaceApp
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
     * Add File entity to collection (one to many).
     *
     * @param \App\Entities\Base\File $file
     * @return \App\Entities\Base\WorkspaceApp
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
     * @return \App\Entities\Base\WorkspaceApp
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
     * Set ScannerApp entity (many to one).
     *
     * @param \App\Entities\Base\ScannerApp $scannerApp
     * @return \App\Entities\Base\WorkspaceApp
     */
    public function setScannerApp(ScannerApp $scannerApp = null)
    {
        $this->scannerApp = $scannerApp;

        return $this;
    }

    /**
     * Get ScannerApp entity (many to one).
     *
     * @return \App\Entities\Base\ScannerApp
     */
    public function getScannerApp()
    {
        return $this->scannerApp;
    }

    /**
     * Set Workspace entity (many to one).
     *
     * @param \App\Entities\Base\Workspace $workspace
     * @return \App\Entities\Base\WorkspaceApp
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

    public function __sleep()
    {
        return array('id', 'name', 'description', 'scanner_app_id', 'workspace_id', 'created_at', 'updated_at');
    }
}