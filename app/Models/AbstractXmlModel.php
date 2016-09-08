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
    protected $openPorts;

    /** @var Collection */
    protected $methodsRequiringAPortId;

    /** @var Collection */
    protected $softwareInformation;

    /** @var SoftwareInformationModel */
    protected $tempSoftwareInformation;

    /** @var Collection */
    protected $exportForAssetMap;

    /** @var Collection */
    protected $exportForVulnerabilityMap;

    /** @var Collection */
    protected $exportForVulnerabilityRefsMap;

    /**
     * AbstractXmlModel constructor.
     */
    public function __construct()
    {
        // Default Model to Entity mappings for Asset data
        $this->exportForAssetMap = new Collection([
            Asset::HOSTNAME      => 'getSanitisedHostname',
            Asset::IP_ADDRESS_V4 => 'getIpV4',
            Asset::IP_ADDRESS_V6 => 'getIpV6',
        ]);

        // Initialise Collection properties
        $this->openPorts               = new Collection();
        $this->methodsRequiringAPortId = new Collection();
        $this->softwareInformation     = new Collection();

        // Initialise the other possible Model to Entity mappings
        $this->exportForVulnerabilityMap       = new Collection();
        $this->exportForVulnerabilityRefsMap   = new Collection();
        $this->exportForOpenPortMap            = new Collection();
        $this->exportForSoftwareInformationMap = new Collection();
    }

    /**
     * @return string
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * Get a sanitised hostname for Asset entries
     *
     * @return string
     */
    public function getSanitisedHostname()
    {
        if (empty($this->hostname)) {
            return $this->hostname;
        }

        // Strip the scheme and the basic auth if it's there so we only store the actual hostname in the Asset entry
        $hostname = preg_replace(
            '~^' . Asset::REGEX_PROTOCOL . '?' . Asset::REGEX_BASIC_AUTH . '?~', '', $this->hostname
        );
        // Strip the port number too
        $hostname = preg_replace('~' . Asset::REGEX_PORT_NUMBER . '~', '', $hostname);

        return $hostname;
    }

    /**
     * @param string $hostname
     */
    public function setHostname(string $hostname)
    {
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
    public function getOpenPorts()
    {
        return $this->openPorts;
    }

    /**
     * @param Collection $openPorts
     */
    public function setOpenPorts(Collection $openPorts)
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
        if (!empty($this->openPorts->get($portId))) {
            return $this->openPorts->get($portId);
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
     * @inheritdoc
     *
     * @return Collection
     */
    public function getMethodsRequiringAPortId(): Collection
    {
        return $this->methodsRequiringAPortId;
    }

    /**
     * @return Collection
     */
    public function getSoftwareInformation(): Collection
    {
        return $this->softwareInformation;
    }

    /**
     * @param Collection $softwareInformation
     */
    public function setSoftwareInformation(Collection $softwareInformation)
    {
        $this->softwareInformation = $softwareInformation;
    }

    /**
     * @return SoftwareInformationModel|mixed
     */
    public function addSoftwareInformationFromTemp()
    {
        $hash = $this->tempSoftwareInformation->getHash();
        if (!empty($this->getSoftwareInformation()->get($hash))) {
            return $this->getSoftwareInformation()->get($hash);
        }

        $this->getSoftwareInformation()->put($hash, $this->tempSoftwareInformation);
        return $this->tempSoftwareInformation;
    }

    /**
     * @param SoftwareInformationModel $softwareInformation
     */
    public function removeSoftwareInformation(SoftwareInformationModel $softwareInformation)
    {
        $hash = $softwareInformation->getHash();
        $this->getSoftwareInformation()->offsetUnset($hash);
    }

    /**
     * @param string $softwareName
     * @return SoftwareInformationModel
     */
    public function setSoftwareName(string $softwareName)
    {
        $this->tempSoftwareInformation->setName($softwareName);
        return $this->tempSoftwareInformation;
    }

    /**
     * @param string $softwareVersion
     * @return SoftwareInformationModel
     */
    public function setSoftwareVersion(string $softwareVersion)
    {
        $this->tempSoftwareInformation->setVersion($softwareVersion);
        return $this->tempSoftwareInformation;
    }

    /**
     * @param string $softwareVendor
     * @return SoftwareInformationModel
     */
    public function setSoftwareVendor(string $softwareVendor)
    {
        $this->tempSoftwareInformation->setVendor($softwareVendor);
        return $this->tempSoftwareInformation;
    }

    /**
     * @return SoftwareInformationModel
     */
    public function getTempSoftwareInformation()
    {
        return $this->tempSoftwareInformation;
    }

    /**
     * @param SoftwareInformationModel $tempSoftwareInformation
     */
    public function setTempSoftwareInformation(SoftwareInformationModel $tempSoftwareInformation)
    {
        $this->tempSoftwareInformation = $tempSoftwareInformation;
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
    public function exportForVulnerability(): Collection
    {
        return $this->mapModelValuesForExport($this->exportForVulnerabilityMap);
    }

    /**
     * @inheritdoc
     *
     * @return Collection
     */
    public function exportForVulnerabilityReference(): Collection
    {
        $vulnerabilityRefs = $this->mapModelValuesForExport($this->exportForVulnerabilityRefsMap);

        // Convert to a numerically indexed Collection so that whether we have a single or multiple entity details
        // exported, we get a Collection formatted in the same way
        return new Collection($vulnerabilityRefs);
    }

    /**
     * @inheritdoc
     *
     * @return Collection
     */
    public function exportOpenPorts(): Collection
    {
        return $this->openPorts->filter(function ($openPort) {
            return !empty($openPort) && $openPort instanceof PortModel;
        })->map(function ($openPort) {
            /** @var $openPort PortModel */
            return $openPort->export();
        });
    }

    /**
     * @inheritdoc
     *
     * @return Collection
     */
    public function exportSoftwareInformation(): Collection
    {
        return $this->softwareInformation->filter(function($software) {
            return !empty($software) && $software instanceof SoftwareInformationModel;
        })->map(function ($software) {
            /** @var $software SoftwareInformationModel */
            return $software->export();
        });
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
        return $mappings->map(function ($getter) {
            // Make sure the given getter method exists
            if (!method_exists($this, $getter)) {
                return null;
            }

            // Call the getter method
            return $this->$getter();

        })->filter(function ($value) {
            // Filter out unset items from the Collection
            return isset($value);
        });
    }
}