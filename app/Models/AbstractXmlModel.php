<?php

namespace App\Models;

use App\Contracts\CollectsScanOutput;
use App\Entities\Asset;
use Illuminate\Support\Collection;

abstract class AbstractXmlModel implements CollectsScanOutput
{
    /** @var string */
    protected $hostname;

    /** @var string */
    protected $ipV4;

    /** @var string */
    protected $ipV6;

    /** @var Collection */
    protected $exportForAssetMap;

    /** @var Collection */
    protected $exportForVulnerabilityMap;

    /** @var Collection */
    protected $exportForVulnerabilityRefsMap;

    /** @var Collection */
    protected $exportForSystemInformationMap;

    /**
     * AbstractXmlModel constructor.
     */
    public function __construct()
    {
        $this->exportForAssetMap = new Collection([
            'hostname'      => 'getHostname',
            'ip_address_v4' => 'getIpV4',
            'ip_address_v6' => 'getIpV6',
        ]);

        $this->exportForVulnerabilityMap     = new Collection();
        $this->exportForVulnerabilityRefsMap = new Collection();
        $this->exportForSystemInformationMap = new Collection();
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
    public function setHostname(string $hostname)
    {
        // Strip the scheme and the basic auth if it's there so we only store the actual hostname in the Asset entry
        $hostname = preg_replace('~^' . Asset::REGEX_PROTOCOL . Asset::REGEX_BASIC_AUTH . '?~', '', $hostname);
        // Strip the port number too
        $hostname = preg_replace('~' . Asset::REGEX_PORT_NUMBER . '~', '', $hostname);
        $this->hostname = $hostname;
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
    public function setIpV4(string $ipV4)
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
    public function setIpV6(string $ipV6)
    {
        $this->ipV6 = $ipV6;
    }

    /**
     * @return Collection
     */
    public function getExportForAssetMap(): Collection
    {
        return $this->exportForAssetMap;
    }

    /**
     * @inheritdoc
     *
     * @return Collection
     */
    public function exportForAsset(): Collection
    {
        return $this->mapModelValuesForExport($this->exportForAssetMap);
    }

    /**
     * @inheritdoc
     *
     * @return Collection
     */
    function exportForVulnerability(): Collection
    {
        return $this->mapModelValuesForExport($this->exportForVulnerabilityMap);
    }

    /**
     * @inheritdoc
     *
     * @return Collection
     */
    function exportForVulnerabilityReference(): Collection
    {
        return $this->mapModelValuesForExport($this->exportForVulnerabilityRefsMap);
    }

    /**
     * @inheritdoc
     *
     * @return Collection
     */
    function exportForSystemInformation(): Collection
    {
        return $this->mapModelValuesForExport($this->exportForSystemInformationMap);
    }

    /**
     * Map the model values to the related entity properties
     *
     * @param Collection $mappings
     * @return Collection
     */
    protected function mapModelValuesForExport(Collection $mappings): Collection
    {
        // If the mappings are empty, just return the empty Collection object
        if ($mappings->isEmpty()) {
            return $mappings;
        }

        // Map the model values using the mapping of entity properties to model getters and then filter out
        // any null/unset keys
        return $mappings->map(function ($getter, $assetField) {
            if (!method_exists($this, $getter)) {
                return null;
            }

            return $this->$getter();

        })->filter(function ($value, $key) {
            return isset($value);
        });
    }
}