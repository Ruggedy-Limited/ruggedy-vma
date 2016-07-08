<?php

namespace App\Models;

use App\Contracts\CollectsScanOutput;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class NmapModel implements CollectsScanOutput
{
    /** @var string */
    protected $hostname;

    /** @var string */
    protected $osVendor;

    /** @var string */
    protected $osVersion;

    /** @var string */
    protected $ipV4;

    /** @var string */
    protected $ipV6;

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

    /**
     * NmapModel constructor.
     */
    public function __construct()
    {
        $this->ports = new Collection();
    }

    /**
     * @return string
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * @param string $hostname
     */
    public function setHostname($hostname)
    {
        $this->hostname = $hostname;
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
    public function getIpV4()
    {
        return $this->ipV4;
    }

    /**
     * @param string $ipV4
     */
    public function setIpV4($ipV4)
    {
        $this->ipV4 = $ipV4;
    }

    /**
     * @return string
     */
    public function getIpV6()
    {
        return $this->ipV6;
    }

    /**
     * @param string $ipV6
     */
    public function setIpV6($ipV6)
    {
        $this->ipV6 = $ipV6;
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
     * @inheritdoc
     *
     * @return Collection
     */
    public function exportForAsset(): Collection
    {
        return new Collection([
            'hostname'      => $this->getHostname(),
            'vendor'        => $this->getOsVendor(),
            'os_version'    => $this->getOsVersion(),
            'ip_address_v4' => $this->getIpV4(),
            'ip_address_v6' => $this->getIpV6(),
            'mac_address'   => $this->getMacAddress(),
            'mac_vendor'    => $this->getMacVendor(),
        ]);
    }
}