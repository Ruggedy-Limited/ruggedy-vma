<?php

namespace App\Models;

use App\Contracts\CollectsScanOutput;
use App\Entities\Asset;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class NmapModel extends AbstractXmlModel implements CollectsScanOutput
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
    protected $ports;

    /** @var Carbon */
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
        $this->ports      = new Collection();
        $this->accuracies = new Collection();
        $this->exportForAssetMap = new Collection([
            'hostname'      => 'getHostname',
            'cpe'           => 'getCpe',
            'vendor'        => 'getOsVendor',
            'os_version'    => 'getOsVersion',
            'ip_address_v4' => 'getIpV4',
            'ip_address_v6' => 'getIpV6',
            'mac_address'   => 'getMacAddress',
            'mac_vendor'    => 'getMacVendor',
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
    public function getPorts()
    {
        return $this->ports;
    }

    /**
     * @param Collection $ports
     */
    public function setPorts($ports)
    {
        $this->ports = $ports;
    }

    /**
     * Add an open port
     *
     * @param PortModel $port
     */
    public function addPort(PortModel $port)
    {
        $this->ports->push($port);
    }

    /**
     * Remove an open port
     *
     * @param PortModel $port
     * @return bool
     */
    public function removePort(PortModel $port)
    {
        $index = $this->ports->search($port, true);
        if (empty($index)) {
            return false;
        }

        $this->ports->pull($index);
        return true;
    }

    /**
     * @return Carbon
     */
    public function getUptime()
    {
        return $this->uptime;
    }

    /**
     * @param Carbon $uptime
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
        $this->lastBoot = $lastBoot;
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