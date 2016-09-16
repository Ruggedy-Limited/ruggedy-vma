<?php

namespace App\Models;

use App\Contracts\CollectsScanOutput;
use App\Entities\Asset;
use App\Entities\Vulnerability;
use Carbon\Carbon;
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

    /** @var Collection */
    protected $vulnerabilities;

    /** @var Vulnerability */
    protected $tempVulnerability;

    /** @var SoftwareInformationModel */
    protected $tempSoftwareInformation;

    /** @var Collection */
    protected $accuracies;

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
        $this->vulnerabilities         = new Collection();
        $this->accuracies              = new Collection();

        // Initialise the other possible Model to Entity mappings
        $this->exportForVulnerabilityMap       = new Collection();
        $this->exportForVulnerabilityRefsMap   = new Collection();
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
    public function getVulnerabilities(): Collection
    {
        return $this->vulnerabilities;
    }

    /**
     * @param Collection $vulnerabilities
     */
    public function setVulnerabilities(Collection $vulnerabilities)
    {
        $this->vulnerabilities = $vulnerabilities;
    }

    /**
     * Add the temporary Vulnerability entity to the Collection for Vulnerabilities for this model
     *
     * @return Vulnerability
     */
    public function addVulnerabilityFromTemp(): Vulnerability
    {
        $id = $this->tempVulnerability->getIdFromScanner();
        if (!empty($this->vulnerabilities->get($id))) {
            return $this->vulnerabilities->get($id);
        }

        $this->vulnerabilities->put($id, $this->tempVulnerability);
        return $this->tempVulnerability;
    }

    /**
     * Remove a Vulnerability from the Collection of Vulnerability
     *
     * @param Vulnerability $vulnerability
     */
    public function removeVulnerability(Vulnerability $vulnerability)
    {
        $this->vulnerabilities->offsetUnset($vulnerability->getIdFromScanner());
    }

    /**
     * @param string $vulnerabilityName
     * @return Vulnerability
     */
    public function setVulnerabilityName(string $vulnerabilityName)
    {
        $this->tempVulnerability->setName($vulnerabilityName);
        return $this->tempVulnerability;
    }

    /**
     * @param string $vulnerabilityId
     * @return Vulnerability
     */
    public function setVulnerabilityId(string $vulnerabilityId)
    {
        $this->tempVulnerability->setIdFromScanner($vulnerabilityId);
        return $this->tempVulnerability;
    }

    /**
     * @param string $exploit
     * @return Vulnerability
     */
    public function setExploit(string $exploit)
    {
        $this->tempVulnerability->setExploitDescription($exploit);
        $this->tempVulnerability->setExploitAvailable(true);

        return $this->tempVulnerability;
    }

    /**
     * @param string $malware
     * @return Vulnerability
     */
    public function setMalware(string $malware)
    {
        $this->tempVulnerability->setMalwareDescription($malware);
        $this->tempVulnerability->setMalwareAvailable(true);

        return $this->tempVulnerability;
    }

    /**
     * @param string $severity
     * @return Vulnerability
     */
    public function setSeverity(string $severity)
    {
        $this->tempVulnerability->setSeverity($severity);

        return $this->tempVulnerability;
    }

    /**
     * @param float $cvssScore
     * @return Vulnerability
     */
    public function setCvssScore(float $cvssScore)
    {
        $this->tempVulnerability->setCvssScore($cvssScore);

        return $this->tempVulnerability;
    }

    /**
     * @param string $description
     * @return Vulnerability
     */
    public function setVulnerabilityDescription(string $description)
    {
        $this->tempVulnerability->setDescription($description);

        return $this->tempVulnerability;
    }

    /**
     * @param string $solution
     * @return Vulnerability
     */
    public function setSolution(string $solution)
    {
        $this->tempVulnerability->setSolution($solution);

        return $this->tempVulnerability;
    }

    /**
     * @param string $genericOutput
     * @return Vulnerability
     */
    public function setGenericOutput(string $genericOutput)
    {
        $this->tempVulnerability->setGenericOutput($genericOutput);

        return $this->tempVulnerability;
    }

    /**
     * @param string|Carbon $publishedDate
     * @return Vulnerability
     */
    public function setPublishedDate($publishedDate)
    {
        if ($publishedDate instanceof Carbon) {
            $this->tempVulnerability->setPublishedDateFromScanner($publishedDate);
            return $this->tempVulnerability;
        }

        $this->tempVulnerability->setPublishedDateFromScanner(new Carbon($publishedDate));

        return $this->tempVulnerability;
    }

    /**
     * @param string|Carbon $lastModifiedDate
     * @return Vulnerability
     */
    public function setLastModifiedDate($lastModifiedDate)
    {
        if ($lastModifiedDate instanceof Carbon) {
            $this->tempVulnerability->setModifiedDateFromScanner($lastModifiedDate);
            return $this->tempVulnerability;
        }

        $this->tempVulnerability->setModifiedDateFromScanner(new Carbon($lastModifiedDate));

        return $this->tempVulnerability;
    }

    /**
     * @return Vulnerability
     */
    public function getTempVulnerability(): Vulnerability
    {
        return $this->tempVulnerability;
    }

    /**
     * @param Vulnerability $tempVulnerability
     */
    public function setTempVulnerability(Vulnerability $tempVulnerability)
    {
        $this->tempVulnerability = $tempVulnerability;
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
    public function exportForVulnerabilities(): Collection
    {
        return $this->exportModelCollection($this->vulnerabilities, Vulnerability::class);
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
        return $this->exportModelCollection($this->openPorts, PortModel::class);
    }

    /**
     * @inheritdoc
     *
     * @return Collection
     */
    public function exportSoftwareInformation(): Collection
    {
        return $this->exportModelCollection($this->softwareInformation, SoftwareInformationModel::class);
    }

    /**
     * Export a model Collection
     *
     * @param Collection $modelCollection
     * @param string $modelClass
     * @return Collection
     */
    protected function exportModelCollection(Collection $modelCollection, string $modelClass): Collection
    {
        return $modelCollection->filter(function($model) use ($modelClass) {
            return !empty($model) && $model instanceof $modelClass;
        })->map(function ($model) {
            return $model->export();
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

        // Filter out any invalid mappings and then map the model values using the
        // mapping of entity properties to model getters
        return $mappings->filter(function ($getter) {
            // Make sure the given getter method exists
            return !empty($getter) && method_exists($this, $getter);
        })->map(function ($getter) {
            // Call the getter method
            return $this->$getter();
        });
    }
}