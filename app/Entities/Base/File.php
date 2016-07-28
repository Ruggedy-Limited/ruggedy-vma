<?php

namespace App\Entities\Base;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * App\Entities\Base\File
 *
 * @ORM\MappedSuperclass
 * @ORM\Table(name="`files`", indexes={@ORM\Index(name="files_user_fk_idx", columns={"`user_id`"}), @ORM\Index(name="files_workspace_fk_idx", columns={"`workspace_id`"}), @ORM\Index(name="files_asset_fk_idx", columns={"`asset_id`"}), @ORM\Index(name="files_scanner_app_fk_idx", columns={"`scanner_app_id`"})})
 */
class File extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(name="`id`", type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

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
     * @ORM\Column(name="`workspace_id`", type="integer", options={"unsigned":true})
     */
    protected $workspace_id;

    /**
     * @ORM\Column(name="`asset_id`", type="integer", nullable=true, options={"unsigned":true})
     */
    protected $asset_id;

    /**
     * @ORM\Column(name="`scanner_app_id`", type="integer", options={"unsigned":true})
     */
    protected $scanner_app_id;

    /**
     * @ORM\Column(name="`processed`", type="smallint", options={"unsigned":true})
     */
    protected $processed;

    /**
     * @ORM\Column(name="`deleted`", type="smallint", options={"unsigned":true})
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
     * @ORM\OneToMany(targetEntity="SystemInformation", mappedBy="file", cascade={"persist"})
     * @ORM\JoinColumn(name="`id`", referencedColumnName="`file_id`", nullable=false)
     */
    protected $systemInformations;

    /**
     * @ORM\OneToMany(targetEntity="Vulnerability", mappedBy="file", cascade={"persist"})
     * @ORM\JoinColumn(name="`id`", referencedColumnName="`file_id`", nullable=false)
     */
    protected $vulnerabilities;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="files", cascade={"persist"})
     * @ORM\JoinColumn(name="`user_id`", referencedColumnName="`id`", nullable=false)
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Workspace", inversedBy="files", cascade={"persist"})
     * @ORM\JoinColumn(name="`workspace_id`", referencedColumnName="`id`", nullable=false)
     */
    protected $workspace;

    /**
     * @ORM\ManyToOne(targetEntity="Asset", inversedBy="files", cascade={"persist"})
     * @ORM\JoinColumn(name="`asset_id`", referencedColumnName="`id`")
     */
    protected $asset;

    /**
     * @ORM\ManyToOne(targetEntity="ScannerApp", inversedBy="files", cascade={"persist"})
     * @ORM\JoinColumn(name="`scanner_app_id`", referencedColumnName="`id`", nullable=false)
     */
    protected $scannerApp;

    public function __construct()
    {
        $this->systemInformations = new ArrayCollection();
        $this->vulnerabilities = new ArrayCollection();
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
     * Set the value of workspace_id.
     *
     * @param integer $workspace_id
     * @return \App\Entities\Base\File
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
     * Set the value of asset_id.
     *
     * @param integer $asset_id
     * @return \App\Entities\Base\File
     */
    public function setAssetId($asset_id)
    {
        $this->asset_id = $asset_id;

        return $this;
    }

    /**
     * Get the value of asset_id.
     *
     * @return integer
     */
    public function getAssetId()
    {
        return $this->asset_id;
    }

    /**
     * Set the value of scanner_app_id.
     *
     * @param integer $scanner_app_id
     * @return \App\Entities\Base\File
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
     * Set the value of processed.
     *
     * @param integer $processed
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
     * @return integer
     */
    public function getProcessed()
    {
        return $this->processed;
    }

    /**
     * Set the value of deleted.
     *
     * @param integer $deleted
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
     * Add SystemInformation entity to collection (one to many).
     *
     * @param \App\Entities\Base\SystemInformation $systemInformation
     * @return \App\Entities\Base\File
     */
    public function addSystemInformation(SystemInformation $systemInformation)
    {
        $this->systemInformations[] = $systemInformation;

        return $this;
    }

    /**
     * Remove SystemInformation entity from collection (one to many).
     *
     * @param \App\Entities\Base\SystemInformation $systemInformation
     * @return \App\Entities\Base\File
     */
    public function removeSystemInformation(SystemInformation $systemInformation)
    {
        $this->systemInformations->removeElement($systemInformation);

        return $this;
    }

    /**
     * Get SystemInformation entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSystemInformations()
    {
        return $this->systemInformations;
    }

    /**
     * Add Vulnerability entity to collection (one to many).
     *
     * @param \App\Entities\Base\Vulnerability $vulnerability
     * @return \App\Entities\Base\File
     */
    public function addVulnerability(Vulnerability $vulnerability)
    {
        $this->vulnerabilities[] = $vulnerability;

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
     * Set Workspace entity (many to one).
     *
     * @param \App\Entities\Base\Workspace $workspace
     * @return \App\Entities\Base\File
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

    /**
     * Set Asset entity (many to one).
     *
     * @param \App\Entities\Base\Asset $asset
     * @return \App\Entities\Base\File
     */
    public function setAsset(Asset $asset = null)
    {
        $this->asset = $asset;

        return $this;
    }

    /**
     * Get Asset entity (many to one).
     *
     * @return \App\Entities\Base\Asset
     */
    public function getAsset()
    {
        return $this->asset;
    }

    /**
     * Set ScannerApp entity (many to one).
     *
     * @param \App\Entities\Base\ScannerApp $scannerApp
     * @return \App\Entities\Base\File
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

    public function __sleep()
    {
        return array('id', 'path', 'format', 'size', 'user_id', 'workspace_id', 'asset_id', 'scanner_app_id', 'processed', 'deleted', 'created_at', 'updated_at');
    }
}