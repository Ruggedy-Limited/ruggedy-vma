<?php

namespace App\Models;

use App\Contracts\CollectsPortInformation;
use App\Contracts\CollectsScanOutput;
use App\Entities\Asset;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class NmapModel extends AbstractXmlModel implements CollectsScanOutput, CollectsPortInformation
{
    /** @var string */
    protected $osVendor;

    /** @var string */
    protected $osVersion;

    /** @var string */
    protected $cpe;

    /** @var string */
    protected $macAddress;

    /** @var string */
    protected $macVendor;

    /** @var Collection */
    protected $openPorts;

    /** @var int */
    protected $uptime;

    /** @var Carbon */
    protected $lastBoot;

    /** @var Collection */
    protected $accuracies;

    /** @var Collection */
    protected $methodsRequiringAPortId;

    /**
     * NmapModel constructor.
     */
    public function __construct()
    {
        // Call the parent constructor
        parent::__construct();

        // Intialise openPorts and accuracies Collection objects
        $this->openPorts         = new Collection();
        $this->accuracies        = new Collection();

        // Override the default Model to Entity mappings for Asset data
        $this->exportForAssetMap = new Collection([
            Asset::HOSTNAME      => 'getHostname',
            Asset::CPE           => 'getCpe',
            Asset::VENDOR        => 'getOsVendor',
            Asset::OS_VERSION    => 'getOsVersion',
            Asset::IP_ADDRESS_V4 => 'getIpV4',
            Asset::IP_ADDRESS_V6 => 'getIpV6',
            Asset::MAC_ADDRESS   => 'getMacAddress',
            Asset::MAC_VENDOR    => 'getMacVendor',
            Asset::UPTIME        => 'getUptime',
            Asset::LAST_BOOT     => 'getLastBoot',
        ]);

        // Set the list of methods that require a PortId as an extra parameter
        $this->methodsRequiringAPortId = new Collection([
            'setPortProtocol',
            'setPortServiceName',
            'setPortServiceProduct',
            'setPortExtraInfo',
            'setPortFingerPrint',
        ]);
    }

    /**
     * @return string
     */
    public function getOsVendor()
    {
        return $this->osVendor;
    }

    /**
     * @param string $osVendor
     */
    public function setOsVendor($osVendor)
    {
        // Validate the vendor and set to Unknown if invalid
        $osVendor = Asset::isValidOsVendor($osVendor) ? $osVendor : Asset::OS_VENDOR_UNKNOWN;
        $this->osVendor = $osVendor;
    }

    /**
     * @return string
     */
    public function getOsVersion()
    {
        return $this->osVersion;
    }

    /**
     * @param string $osVersion
     */
    public function setOsVersion($osVersion)
    {
        $this->osVersion = $osVersion;
    }

    /**
     * @return string
     */
    public function getCpe()
    {
        return $this->cpe;
    }

    /**
     * @param string $cpe
     */
    public function setCpe($cpe)
    {
        $this->cpe = $cpe;
    }

    /**
     * @return string
     */
    public function getMacAddress()
    {
        return $this->macAddress;
    }

    /**
     * @param string $macAddress
     */
    public function setMacAddress($macAddress)
    {
        $this->macAddress = $macAddress;
    }

    /**
     * @return array
     */
    public function getMacVendor()
    {
        return $this->macVendor;
    }

    /**
     * @param string $macVendor
     */
    public function setMacVendor($macVendor)
    {
        $this->macVendor = $macVendor;
    }

    /**
     * @return Collection
     */
    public function getOpenPorts()
    {
        return $this->openPorts;
    }

    /**
     * @param Collection $openPorts
     */
    public function setOpenPorts($openPorts)
    {
        $this->openPorts = $openPorts;
    }

    /**
     * Add an open port
     *
     * @param PortModel $port
     * @param int $portId
     * @return Collection
     */
    public function addPort(PortModel $port, int $portId): Collection
    {
        // Make sure we have a valid port ID
        if (!isset($portId)) {
            return $this->openPorts;
        }

        // Add the model to the openPorts Collection, using the portId as the offset
        return $this->openPorts->put($portId, $port);
    }

    /**
     * Remove an open port
     *
     * @param int $portId
     * @return bool
     * @internal param PortModel $port
     */
    public function removePort(int $portId)
    {
        $this->openPorts->offsetUnset($portId);
    }

    /**
     * Create a new PortModel for the given portId if one does not yet exist
     *
     * @param int $portId
     * @return PortModel
     */
    public function setPortId(int $portId): PortModel
    {
        // Check if there is already an Open Port at the given portId offset in the Collection
        if (!empty($this->getOpenPorts()->get($portId))) {
            return $this->getOpenPorts()->get($portId);
        }

        // Create a new PortModel, set the ID and add to the Collection of ports on this Model
        $portModel = new PortModel();
        $portModel->setPortId($portId);
        $this->addPort($portModel, $portId);

        return $portModel;
    }

    /**
     * Set the protocol for the given port ID
     *
     * @param int $portId
     * @param string $portProtocol
     */
    public function setPortProtocol(int $portId, string $portProtocol)
    {
        $portModel = $this->setPortId($portId);
        $portModel->setProtocol($portProtocol);
    }

    /**
     * Set the service name for the given port ID
     *
     * @param int $portId
     * @param string $portServiceName
     * @internal param string $portProtocol
     */
    public function setPortServiceName(int $portId, string $portServiceName)
    {
        $portModel = $this->setPortId($portId);
        $portModel->setServiceName($portServiceName);
    }

    /**
     * Set the service product for the given port ID
     *
     * @param int $portId
     * @param string $portServiceProduct
     * @internal param string $portProtocol
     */
    public function setPortServiceProduct(int $portId, string $portServiceProduct)
    {
        $portModel = $this->setPortId($portId);
        $portModel->setServiceProduct($portServiceProduct);
    }

    /**
     * Set the extra information for the given port ID
     *
     * @param int $portId
     * @param string $portExtraInfo
     * @internal param string $portProtocol
     */
    public function setPortExtraInfo(int $portId, string $portExtraInfo)
    {
        $portModel = $this->setPortId($portId);
        $portModel->setServiceExtraInformation($portExtraInfo);
    }

    /**
     * Set the finger print for the given port ID
     *
     * @param int $portId
     * @param string $portFingerPrint
     * @internal param string $portProtocol
     */
    public function setPortFingerPrint(int $portId, string $portFingerPrint)
    {
        $portModel = $this->setPortId($portId);
        $portModel->setServiceFingerPrint($portFingerPrint);
    }

    /**
     * Set the service banner for the given port ID
     *
     * @param int $portId
     * @param string $portServiceBanner
     * @internal param string $portProtocol
     */
    public function setPortServiceBanner(int $portId, string $portServiceBanner)
    {
        $portModel = $this->setPortId($portId);
        $portModel->setServiceBanner($portServiceBanner);
    }

    /**
     * @return int
     */
    public function getUptime()
    {
        return $this->uptime;
    }

    /**
     * @param int $uptime
     */
    public function setUptime($uptime)
    {
        $this->uptime = $uptime;
    }

    /**
     * @return Carbon
     */
    public function getLastBoot()
    {
        return $this->lastBoot;
    }

    /**
     * @param Carbon $lastBoot
     */
    public function setLastBoot($lastBoot)
    {
        if ($lastBoot instanceof Carbon) {
            $this->lastBoot = $lastBoot;
            return;
        }

        $this->lastBoot = new Carbon($lastBoot);
    }

    /**
     * @return Collection
     */
    public function getAccuracies()
    {
        return $this->accuracies;
    }

    /**
     * Get the current accuracy for the given field
     *
     * @param string $field
     * @return int
     */
    public function getCurrentAccuracyFor(string $field): int
    {
        return $this->accuracies->get($field, 0);
    }

    /**
     * @param Collection $accuracies
     */
    public function setAccuracies($accuracies)
    {
        $this->accuracies = $accuracies;
    }

    /**
     * Set the accuracy for the given field
     *
     * @param string $field
     * @param int $accuracy
     */
    public function setCurrentAccuracyFor(string $field, int $accuracy)
    {
        $this->accuracies->put($field, $accuracy);
    }

    /**
     * @inheritdoc
     *
     * @return Collection
     */
    public function getMethodsRequiringAPortId(): Collection
    {
        return $this->methodsRequiringAPortId;
    }

    /**
     * @inheritdoc
     *
     * @return Collection
     */
    function exportForOpenPort(): Collection
    {
        return $this->openPorts->map(function ($openPort, $portId) {
            if (empty($openPort) || !($openPort instanceof PortModel)) {
                return null;
            }

            return $openPort->exportForOpenPort();
        })->filter(function ($openPort, $offset) {
            return isset($openPort);
        });
    }
}