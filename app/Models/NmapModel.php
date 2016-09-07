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

    /** @var int */
    protected $uptime;

    /** @var Carbon */
    protected $lastBoot;

    /** @var Collection */
    protected $accuracies;

    /**
     * NmapModel constructor.
     */
    public function __construct()
    {
        // Call the parent constructor
        parent::__construct();

        // Intialise openPorts and accuracies Collection objects
        $this->openPorts  = new Collection();
        $this->accuracies = new Collection();

        // Override the default Model to Entity mappings for Asset data
        $this->exportForAssetMap = new Collection([
            Asset::HOSTNAME      => 'getSanitisedHostname',
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
}