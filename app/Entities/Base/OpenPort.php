<?php

namespace App\Entities\Base;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entities\Base\OpenPort
 *
 * @ORM\MappedSuperclass
 * @ORM\Table(name="`open_ports`", indexes={@ORM\Index(name="open_ports_asset_fk_idx", columns={"`asset_id`"})})
 */
class OpenPort extends AbstractEntity
{
    /** Table name constant */
    const TABLE_NAME = 'open_ports';

    /** Column name constants */
    const NUMBER               = 'number';
    const PROTOCOL             = 'protocol';
    const SERVICE_NAME         = 'service_name';
    const SERVICE_PRODUCT      = 'service_product';
    const SERVICE_EXTRA_INFO   = 'service_extra_info';
    const SERVICE_FINGER_PRINT = 'service_finger_print';
    const SERVICE_BANNER       = 'service_banner';
    const SERVICE_MESSAGE      = 'service_message';
    const ASSET_ID             = 'asset_id';
    const ASSET                = 'asset';

    /**
     * @ORM\Id
     * @ORM\Column(name="`id`", type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="`number`", type="integer", nullable=true, options={"unsigned":true})
     */
    protected $number;

    /**
     * @ORM\Column(name="`protocol`", type="string", length=45, nullable=true)
     */
    protected $protocol;

    /**
     * @ORM\Column(name="`service_name`", type="string", length=45, nullable=true)
     */
    protected $service_name;

    /**
     * @ORM\Column(name="`service_product`", type="string", length=150, nullable=true)
     */
    protected $service_product;

    /**
     * @ORM\Column(name="`service_extra_info`", type="text", nullable=true)
     */
    protected $service_extra_info;

    /**
     * @ORM\Column(name="`service_finger_print`", type="string", length=255, nullable=true)
     */
    protected $service_finger_print;

    /**
     * @ORM\Column(name="`service_banner`", type="string", length=255, nullable=true)
     */
    protected $service_banner;

    /**
     * @ORM\Column(name="`service_message`", type="string", length=255, nullable=true)
     */
    protected $service_message;

    /**
     * @ORM\Column(name="`asset_id`", type="integer", options={"unsigned":true})
     */
    protected $asset_id;

    /**
     * @ORM\Column(name="`created_at`", type="datetime")
     */
    protected $created_at;

    /**
     * @ORM\Column(name="`updated_at`", type="datetime")
     */
    protected $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity="Asset", inversedBy="openPorts", cascade={"persist"})
     * @ORM\JoinColumn(name="`asset_id`", referencedColumnName="`id`", nullable=false, onDelete="CASCADE")
     */
    protected $asset;

    public function __construct()
    {
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entities\Base\OpenPort
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
     * Set the value of number.
     *
     * @param integer $number
     * @return \App\Entities\Base\OpenPort
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get the value of number.
     *
     * @return integer
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set the value of protocol.
     *
     * @param string $protocol
     * @return \App\Entities\Base\OpenPort
     */
    public function setProtocol($protocol)
    {
        $this->protocol = $protocol;

        return $this;
    }

    /**
     * Get the value of protocol.
     *
     * @return string
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * Set the value of service_name.
     *
     * @param string $service_name
     * @return \App\Entities\Base\OpenPort
     */
    public function setServiceName($service_name)
    {
        $this->service_name = $service_name;

        return $this;
    }

    /**
     * Get the value of service_name.
     *
     * @return string
     */
    public function getServiceName()
    {
        return $this->service_name;
    }

    /**
     * Set the value of service_product.
     *
     * @param string $service_product
     * @return \App\Entities\Base\OpenPort
     */
    public function setServiceProduct($service_product)
    {
        $this->service_product = $service_product;

        return $this;
    }

    /**
     * Get the value of service_product.
     *
     * @return string
     */
    public function getServiceProduct()
    {
        return $this->service_product;
    }

    /**
     * Set the value of service_extra_info.
     *
     * @param string $service_extra_info
     * @return \App\Entities\Base\OpenPort
     */
    public function setServiceExtraInfo($service_extra_info)
    {
        $this->service_extra_info = $service_extra_info;

        return $this;
    }

    /**
     * Get the value of service_extra_info.
     *
     * @return string
     */
    public function getServiceExtraInfo()
    {
        return $this->service_extra_info;
    }

    /**
     * Set the value of service_finger_print.
     *
     * @param string $service_finger_print
     * @return \App\Entities\Base\OpenPort
     */
    public function setServiceFingerPrint($service_finger_print)
    {
        $this->service_finger_print = $service_finger_print;

        return $this;
    }

    /**
     * Get the value of service_finger_print.
     *
     * @return string
     */
    public function getServiceFingerPrint()
    {
        return $this->service_finger_print;
    }

    /**
     * Set the value of service_banner.
     *
     * @param string $service_banner
     * @return \App\Entities\Base\OpenPort
     */
    public function setServiceBanner($service_banner)
    {
        $this->service_banner = $service_banner;

        return $this;
    }

    /**
     * Get the value of service_banner.
     *
     * @return string
     */
    public function getServiceBanner()
    {
        return $this->service_banner;
    }

    /**
     * Set the value of service_message.
     *
     * @param string $service_message
     * @return \App\Entities\Base\OpenPort
     */
    public function setServiceMessage($service_message)
    {
        $this->service_message = $service_message;

        return $this;
    }

    /**
     * Get the value of service_message.
     *
     * @return string
     */
    public function getServiceMessage()
    {
        return $this->service_message;
    }

    /**
     * Set the value of asset_id.
     *
     * @param integer $asset_id
     * @return \App\Entities\Base\OpenPort
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
     * Set the value of created_at.
     *
     * @param \DateTime $created_at
     * @return \App\Entities\Base\OpenPort
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
     * @return \App\Entities\Base\OpenPort
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
     * @return \App\Entities\Base\OpenPort
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

    public function __sleep()
    {
        return array('id', 'number', 'protocol', 'service_name', 'service_product', 'service_extra_info', 'service_finger_print', 'service_banner', 'service_message', 'asset_id', 'created_at', 'updated_at');
    }
}