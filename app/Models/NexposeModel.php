<?php

namespace App\Models;

use App\Contracts\CollectsPortInformation;
use App\Contracts\CollectsScanOutput;
use App\Entities\Asset;
use App\Entities\Vulnerability;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class NexposeModel extends AbstractXmlModel implements CollectsScanOutput, CollectsPortInformation
{
    /** @var string */
    protected $osVendor;

    /** @var string */
    protected $osVersion;

    /** @var string */
    protected $osProduct;

    /** @var string */
    protected $cpe;

    /** @var string */
    protected $macAddress;

    /** @var Collection */
    protected $openPorts;

    /** @var Collection */
    protected $softwareInformation;

    /** @var SoftwareInformationModel */
    protected $tempSoftwareInformation;

    /** @var string */
    protected $vulnerabilityName;

    /** @var string */
    protected $vulnerabilityId;

    /** @var string */
    protected $exploit;

    /** @var string */
    protected $malware;

    /** @var string */
    protected $severity;

    /** @var float */
    protected $cvssScore;

    /** @var string */
    protected $description;

    /** @var string */
    protected $solution;

    /** @var string */
    protected $testInformation;

    /** @var Carbon */
    protected $publishedDate;

    /** @var Carbon */
    protected $lastModifiedDate;

    /** @var Collection */
    protected $accuracies;

    /** @var Collection */
    protected $methodsRequiringAPortId;

    /**
     * NexposeModel constructor.
     */
    public function __construct()
    {
        // Call the parent constructor
        parent::__construct();

        // Intialise openPorts and accuracies Collection objects
        $this->openPorts           = new Collection();
        $this->softwareInformation = new Collection();
        $this->accuracies          = new Collection();

        $this->exportForVulnerabilityMap = new Collection([
            Vulnerability::ID_FROM_SCANNER             => $this->getVulnerabilityId(),
            Vulnerability::NAME                        => $this->getVulnerabilityName(),
            Vulnerability::EXPLOIT_AVAILABLE           => $this->isExploitAvailable(),
            Vulnerability::EXPLOIT_DESCRIPTION         => $this->getExploit(),
            Vulnerability::MALWARE_AVAILABLE           => $this->isMalwareAvailable(),
            Vulnerability::MALWARE_DESCRIPTION         => $this->getMalware(),
            Vulnerability::SEVERITY                    => $this->getSeverity(),
            Vulnerability::CVSS_SCORE                  => $this->getCvssScore(),
            Vulnerability::DESCRIPTION                 => $this->getDescription(),
            Vulnerability::SOLUTION                    => $this->getSolution(),
            Vulnerability::GENERIC_OUTPUT              => $this->getTestInformation(),
            Vulnerability::PUBLISHED_DATE_FROM_SCANNER => $this->getPublishedDate(),
            Vulnerability::MODIFIED_DATE_FROM_SCANNER  => $this->getLastModifiedDate(),
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
     * @inheritdoc
     *
     * @return Collection
     */
    public function getMethodsRequiringAPortId(): Collection
    {
        return new Collection([]);
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
    public function setOsVendor(string $osVendor)
    {
        $this->osVendor = $osVendor;
    }

    /**
     * @return string
     */
    public function getOsProduct()
    {
        return $this->osProduct;
    }

    /**
     * @param string $osProduct
     */
    public function setOsProduct(string $osProduct)
    {
        $this->osProduct = $osProduct;
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
    public function setOsVersion(string $osVersion)
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
    public function setCpe(string $cpe)
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
    public function setMacAddress(string $macAddress)
    {
        $macAddress = $this->sanitiseMacAddress($macAddress);
        $this->macAddress = $macAddress;
    }

    /**
     * Sanitise the mac addresses found in the Nexpose scan by adding colons after every second character
     *
     * @param string $macAddress
     * @return string|null
     */
    protected function sanitiseMacAddress(string $macAddress)
    {
        if (empty($macAddress) || preg_match(Asset::REGEX_MAC_ADDRESS, $macAddress)) {
            return $macAddress;
        }

        $sanitisedMacAddress = '';
        for ($charCount = 0; $charCount < strlen($macAddress); $charCount++) {
            if ($charCount !== 0 && $charCount % 2 === 0) {
                $sanitisedMacAddress .= ':';
            }

            $sanitisedMacAddress .= $macAddress{$charCount};
        }

        if (!preg_match(Asset::REGEX_MAC_ADDRESS, $sanitisedMacAddress)) {
            return null;
        }

        return $sanitisedMacAddress;
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
     * @return string
     */
    public function getVulnerabilityName()
    {
        return $this->vulnerabilityName;
    }

    /**
     * @param string $vulnerabilityName
     */
    public function setVulnerabilityName(string $vulnerabilityName)
    {
        $this->vulnerabilityName = $vulnerabilityName;
    }

    /**
     * @return string
     */
    public function getVulnerabilityId()
    {
        return $this->vulnerabilityId;
    }

    /**
     * @param string $vulnerabilityId
     */
    public function setVulnerabilityId(string $vulnerabilityId)
    {
        $this->vulnerabilityId = $vulnerabilityId;
    }

    /**
     * @return string
     */
    public function getExploit()
    {
        return $this->exploit;
    }

    /**
     * @return bool
     */
    public function isExploitAvailable()
    {
        return !empty($this->exploit);
    }

    /**
     * @param string $exploit
     */
    public function setExploit(string $exploit)
    {
        $this->exploit = $exploit;
    }

    /**
     * @return string
     */
    public function getMalware()
    {
        return $this->malware;
    }

    /**
     * @return bool
     */
    public function isMalwareAvailable()
    {
        return !empty($this->malware);
    }

    /**
     * @param string $malware
     */
    public function setMalware(string $malware)
    {
        $this->malware = $malware;
    }

    /**
     * @return string
     */
    public function getSeverity()
    {
        return $this->severity;
    }

    /**
     * @param string $severity
     */
    public function setSeverity(string $severity)
    {
        $this->severity = $severity;
    }

    /**
     * @return float
     */
    public function getCvssScore()
    {
        return $this->cvssScore;
    }

    /**
     * @param float $cvssScore
     */
    public function setCvssScore(float $cvssScore)
    {
        $this->cvssScore = $cvssScore;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getSolution()
    {
        return $this->solution;
    }

    /**
     * @param string $solution
     */
    public function setSolution(string $solution)
    {
        $this->solution = $solution;
    }

    /**
     * @return string
     */
    public function getTestInformation()
    {
        return $this->testInformation;
    }

    /**
     * @param string $testInformation
     */
    public function setTestInformation(string $testInformation)
    {
        $this->testInformation = $testInformation;
    }

    /**
     * @return Carbon
     */
    public function getPublishedDate()
    {
        return $this->publishedDate;
    }

    /**
     * @param Carbon $publishedDate
     */
    public function setPublishedDate(Carbon $publishedDate)
    {
        $this->publishedDate = $publishedDate;
    }

    /**
     * @return Carbon
     */
    public function getLastModifiedDate()
    {
        return $this->lastModifiedDate;
    }

    /**
     * @param Carbon $lastModifiedDate
     */
    public function setLastModifiedDate(Carbon $lastModifiedDate)
    {
        $this->lastModifiedDate = $lastModifiedDate;
    }

    /**
     * @return Collection
     */
    public function getAccuracies()
    {
        return $this->accuracies;
    }

    /**
     * @param Collection $accuracies
     */
    public function setAccuracies(Collection $accuracies)
    {
        $this->accuracies = $accuracies;
    }

    /**
     * @inheritdoc
     *
     * @return Collection
     */
    function exportOpenPorts(): Collection
    {
        return $this->openPorts->map(function ($openPort, $portId) {
            if (empty($openPort) || !($openPort instanceof PortModel)) {
                return null;
            }

            return $openPort->export();
        })->filter(function ($openPort, $offset) {
            return isset($openPort);
        });
    }
}