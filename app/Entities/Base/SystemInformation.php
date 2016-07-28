<?php

namespace App\Entities\Base;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entities\Base\SystemInformation
 *
 * @ORM\MappedSuperclass
 * @ORM\Table(name="`system_information`", indexes={@ORM\Index(name="system_information_asset_fk_idx", columns={"`asset_id`"}), @ORM\Index(name="system_information_file_fk_idx", columns={"`file_id`"})})
 */
class SystemInformation extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(name="`id`", type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="`open_port`", type="integer", nullable=true, options={"unsigned":true})
     */
    protected $open_port;

    /**
     * @ORM\Column(name="`port_protocol`", type="string", length=45, nullable=true)
     */
    protected $port_protocol;

    /**
     * @ORM\Column(name="`port_service`", type="string", length=45, nullable=true)
     */
    protected $port_service;

    /**
     * @ORM\Column(name="`port_srv_information`", type="text", nullable=true)
     */
    protected $port_srv_information;

    /**
     * @ORM\Column(name="`port_srv_banner`", type="string", length=150, nullable=true)
     */
    protected $port_srv_banner;

    /**
     * @ORM\Column(name="`uptime`", type="string", length=30, nullable=true)
     */
    protected $uptime;

    /**
     * @ORM\Column(name="`last_boot`", type="datetime", nullable=true)
     */
    protected $last_boot;

    /**
     * @ORM\Column(name="`asset_id`", type="integer", options={"unsigned":true})
     */
    protected $asset_id;

    /**
     * @ORM\Column(name="`file_id`", type="integer", options={"unsigned":true})
     */
    protected $file_id;

    /**
     * @ORM\Column(name="`created_at`", type="datetime")
     */
    protected $created_at;

    /**
     * @ORM\Column(name="`updated_at`", type="datetime")
     */
    protected $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity="Asset", inversedBy="systemInformations", cascade={"persist"})
     * @ORM\JoinColumn(name="`asset_id`", referencedColumnName="`id`", nullable=false)
     */
    protected $asset;

    /**
     * @ORM\ManyToOne(targetEntity="File", inversedBy="systemInformations", cascade={"persist"})
     * @ORM\JoinColumn(name="`file_id`", referencedColumnName="`id`", nullable=false)
     */
    protected $file;

    public function __construct()
    {
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entities\Base\SystemInformation
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
     * Set the value of open_port.
     *
     * @param integer $open_port
     * @return \App\Entities\Base\SystemInformation
     */
    public function setOpenPort($open_port)
    {
        $this->open_port = $open_port;

        return $this;
    }

    /**
     * Get the value of open_port.
     *
     * @return integer
     */
    public function getOpenPort()
    {
        return $this->open_port;
    }

    /**
     * Set the value of port_protocol.
     *
     * @param string $port_protocol
     * @return \App\Entities\Base\SystemInformation
     */
    public function setPortProtocol($port_protocol)
    {
        $this->port_protocol = $port_protocol;

        return $this;
    }

    /**
     * Get the value of port_protocol.
     *
     * @return string
     */
    public function getPortProtocol()
    {
        return $this->port_protocol;
    }

    /**
     * Set the value of port_service.
     *
     * @param string $port_service
     * @return \App\Entities\Base\SystemInformation
     */
    public function setPortService($port_service)
    {
        $this->port_service = $port_service;

        return $this;
    }

    /**
     * Get the value of port_service.
     *
     * @return string
     */
    public function getPortService()
    {
        return $this->port_service;
    }

    /**
     * Set the value of port_srv_information.
     *
     * @param string $port_srv_information
     * @return \App\Entities\Base\SystemInformation
     */
    public function setPortSrvInformation($port_srv_information)
    {
        $this->port_srv_information = $port_srv_information;

        return $this;
    }

    /**
     * Get the value of port_srv_information.
     *
     * @return string
     */
    public function getPortSrvInformation()
    {
        return $this->port_srv_information;
    }

    /**
     * Set the value of port_srv_banner.
     *
     * @param string $port_srv_banner
     * @return \App\Entities\Base\SystemInformation
     */
    public function setPortSrvBanner($port_srv_banner)
    {
        $this->port_srv_banner = $port_srv_banner;

        return $this;
    }

    /**
     * Get the value of port_srv_banner.
     *
     * @return string
     */
    public function getPortSrvBanner()
    {
        return $this->port_srv_banner;
    }

    /**
     * Set the value of uptime.
     *
     * @param string $uptime
     * @return \App\Entities\Base\SystemInformation
     */
    public function setUptime($uptime)
    {
        $this->uptime = $uptime;

        return $this;
    }

    /**
     * Get the value of uptime.
     *
     * @return string
     */
    public function getUptime()
    {
        return $this->uptime;
    }

    /**
     * Set the value of last_boot.
     *
     * @param \DateTime $last_boot
     * @return \App\Entities\Base\SystemInformation
     */
    public function setLastBoot($last_boot)
    {
        $this->last_boot = $last_boot;

        return $this;
    }

    /**
     * Get the value of last_boot.
     *
     * @return \DateTime
     */
    public function getLastBoot()
    {
        return $this->last_boot;
    }

    /**
     * Set the value of asset_id.
     *
     * @param integer $asset_id
     * @return \App\Entities\Base\SystemInformation
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
     * Set the value of file_id.
     *
     * @param integer $file_id
     * @return \App\Entities\Base\SystemInformation
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
     * Set the value of created_at.
     *
     * @param \DateTime $created_at
     * @return \App\Entities\Base\SystemInformation
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
     * @return \App\Entities\Base\SystemInformation
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
     * Set Asset entity (many to one).
     *
     * @param \App\Entities\Base\Asset $asset
     * @return \App\Entities\Base\SystemInformation
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
     * Set File entity (many to one).
     *
     * @param \App\Entities\Base\File $file
     * @return \App\Entities\Base\SystemInformation
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

    public function __sleep()
    {
        return array('id', 'open_port', 'port_protocol', 'port_service', 'port_srv_information', 'port_srv_banner', 'uptime', 'last_boot', 'asset_id', 'file_id', 'created_at', 'updated_at');
    }
}