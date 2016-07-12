<?php

namespace App\Entities\Base;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * App\Entities\Base\Asset
 *
 * @ORM\MappedSuperclass
 * @ORM\Table(name="`assets`", indexes={@ORM\Index(name="assets_workspace_fk_idx", columns={"`workspace_id`"}), @ORM\Index(name="assets_user_fk_idx", columns={"`user_id`"})})
 */
class Asset extends AbstractEntity
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
     * @ORM\Column(name="`cpe`", type="string", length=255, nullable=true)
     */
    protected $cpe;

    /**
     * @ORM\Column(name="`vendor`", type="string", length=45, nullable=true)
     */
    protected $vendor;

    /**
     * @ORM\Column(name="`ip_address_v4`", type="string", length=12, nullable=true)
     */
    protected $ip_address_v4;

    /**
     * @ORM\Column(name="`ip_address_v6`", type="string", length=45, nullable=true)
     */
    protected $ip_address_v6;

    /**
     * @ORM\Column(name="`hostname`", type="string", length=255, nullable=true)
     */
    protected $hostname;

    /**
     * @ORM\Column(name="`mac_address`", type="string", length=25, nullable=true)
     */
    protected $mac_address;

    /**
     * @ORM\Column(name="`mac_vendor`", type="string", length=100, nullable=true)
     */
    protected $mac_vendor;

    /**
     * @ORM\Column(name="`os_version`", type="string", length=20, nullable=true)
     */
    protected $os_version;

    /**
     * @ORM\Column(name="`netbios`", type="string", length=16, nullable=true)
     */
    protected $netbios;

    /**
     * @ORM\Column(name="`workspace_id`", type="integer", options={"unsigned":true})
     */
    protected $workspace_id;

    /**
     * @ORM\Column(name="`user_id`", type="integer", options={"unsigned":true})
     */
    protected $user_id;

    /**
     * @ORM\Column(name="`suppressed`", type="boolean", options={"unsigned":true})
     */
    protected $suppressed;

    /**
     * @ORM\Column(name="`deleted`", type="boolean", options={"unsigned":true})
     */
    protected $deleted;

    /**
     * @ORM\Column(name="`created_at`", type="datetime")
     */
    protected $created_at;

    /**
     * @ORM\Column(name="`updated_at`", type="datetime", nullable=true)
     */
    protected $updated_at;

    /**
     * @ORM\OneToMany(targetEntity="File", mappedBy="asset", cascade={"persist"})
     * @ORM\JoinColumn(name="`id`", referencedColumnName="`asset_id`", nullable=false)
     */
    protected $files;

    /**
     * @ORM\ManyToOne(targetEntity="Workspace", inversedBy="assets", cascade={"persist"})
     * @ORM\JoinColumn(name="`workspace_id`", referencedColumnName="`id`", nullable=false)
     */
    protected $workspace;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="assets", cascade={"persist"})
     * @ORM\JoinColumn(name="`user_id`", referencedColumnName="`id`", nullable=false)
     */
    protected $user;

    public function __construct()
    {
        $this->files = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entities\Base\Asset
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
     * @return \App\Entities\Base\Asset
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
     * Set the value of cpe.
     *
     * @param string $cpe
     * @return \App\Entities\Base\Asset
     */
    public function setCpe($cpe)
    {
        $this->cpe = $cpe;

        return $this;
    }

    /**
     * Get the value of cpe.
     *
     * @return string
     */
    public function getCpe()
    {
        return $this->cpe;
    }

    /**
     * Set the value of vendor.
     *
     * @param string $vendor
     * @return \App\Entities\Base\Asset
     */
    public function setVendor($vendor)
    {
        $this->vendor = $vendor;

        return $this;
    }

    /**
     * Get the value of vendor.
     *
     * @return string
     */
    public function getVendor()
    {
        return $this->vendor;
    }

    /**
     * Set the value of ip_address_v4.
     *
     * @param string $ip_address_v4
     * @return \App\Entities\Base\Asset
     */
    public function setIpAddressV4($ip_address_v4)
    {
        $this->ip_address_v4 = $ip_address_v4;

        return $this;
    }

    /**
     * Get the value of ip_address_v4.
     *
     * @return string
     */
    public function getIpAddressV4()
    {
        return $this->ip_address_v4;
    }

    /**
     * Set the value of ip_address_v6.
     *
     * @param string $ip_address_v6
     * @return \App\Entities\Base\Asset
     */
    public function setIpAddressV6($ip_address_v6)
    {
        $this->ip_address_v6 = $ip_address_v6;

        return $this;
    }

    /**
     * Get the value of ip_address_v6.
     *
     * @return string
     */
    public function getIpAddressV6()
    {
        return $this->ip_address_v6;
    }

    /**
     * Set the value of hostname.
     *
     * @param string $hostname
     * @return \App\Entities\Base\Asset
     */
    public function setHostname($hostname)
    {
        $this->hostname = $hostname;

        return $this;
    }

    /**
     * Get the value of hostname.
     *
     * @return string
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * Set the value of mac_address.
     *
     * @param string $mac_address
     * @return \App\Entities\Base\Asset
     */
    public function setMacAddress($mac_address)
    {
        $this->mac_address = $mac_address;

        return $this;
    }

    /**
     * Get the value of mac_address.
     *
     * @return string
     */
    public function getMacAddress()
    {
        return $this->mac_address;
    }

    /**
     * Set the value of mac_vendor.
     *
     * @param string $mac_vendor
     * @return \App\Entities\Base\Asset
     */
    public function setMacVendor($mac_vendor)
    {
        $this->mac_vendor = $mac_vendor;

        return $this;
    }

    /**
     * Get the value of mac_vendor.
     *
     * @return string
     */
    public function getMacVendor()
    {
        return $this->mac_vendor;
    }

    /**
     * Set the value of os_version.
     *
     * @param string $os_version
     * @return \App\Entities\Base\Asset
     */
    public function setOsVersion($os_version)
    {
        $this->os_version = $os_version;

        return $this;
    }

    /**
     * Get the value of os_version.
     *
     * @return string
     */
    public function getOsVersion()
    {
        return $this->os_version;
    }

    /**
     * Set the value of netbios.
     *
     * @param string $netbios
     * @return \App\Entities\Base\Asset
     */
    public function setNetbios($netbios)
    {
        $this->netbios = $netbios;

        return $this;
    }

    /**
     * Get the value of netbios.
     *
     * @return string
     */
    public function getNetbios()
    {
        return $this->netbios;
    }

    /**
     * Set the value of workspace_id.
     *
     * @param integer $workspace_id
     * @return \App\Entities\Base\Asset
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
     * Set the value of user_id.
     *
     * @param integer $user_id
     * @return \App\Entities\Base\Asset
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
     * Set the value of suppressed.
     *
     * @param boolean $suppressed
     * @return \App\Entities\Base\Asset
     */
    public function setSuppressed($suppressed)
    {
        $this->suppressed = $suppressed;

        return $this;
    }

    /**
     * Get the value of suppressed.
     *
     * @return boolean
     */
    public function getSuppressed()
    {
        return $this->suppressed;
    }

    /**
     * Set the value of deleted.
     *
     * @param boolean $deleted
     * @return \App\Entities\Base\Asset
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
     * @return \App\Entities\Base\Asset
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
     * @return \App\Entities\Base\Asset
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
     * @return \App\Entities\Base\Asset
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
     * @return \App\Entities\Base\Asset
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
     * Set Workspace entity (many to one).
     *
     * @param \App\Entities\Base\Workspace $workspace
     * @return \App\Entities\Base\Asset
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
     * Set User entity (many to one).
     *
     * @param \App\Entities\Base\User $user
     * @return \App\Entities\Base\Asset
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
        return array('id', 'name', 'cpe', 'vendor', 'ip_address_v4', 'ip_address_v6', 'hostname', 'mac_address', 'mac_vendor', 'os_version', 'netbios', 'workspace_id', 'user_id', 'suppressed', 'deleted', 'created_at', 'updated_at');
    }
}