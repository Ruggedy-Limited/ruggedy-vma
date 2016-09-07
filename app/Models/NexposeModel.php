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

        // Set Asset export mappings for additional asset-related data
        $this->exportForAssetMap->put(Asset::MAC_ADDRESS, 'getMacAddress');
        $this->exportForAssetMap->put(Asset::VENDOR, 'getOsVendor');
        $this->exportForAssetMap->put(Asset::OS_VERSION, 'getOsVersion');

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
        if (!empty($this->osVersion)) {
            return $this->osVersion;
        }

        return $this->osProduct;
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

        // Insert colons after every 2 characters
        $sanitisedMacAddress = '';
        for ($charCount = 0; $charCount < strlen($macAddress); $charCount++) {
            if ($charCount !== 0 && $charCount % 2 === 0) {
                $sanitisedMacAddress .= ':';
            }

            $sanitisedMacAddress .= $macAddress{$charCount};
        }

        // Validate the sanitised MAC address against the regex
        if (!preg_match(Asset::REGEX_MAC_ADDRESS, $sanitisedMacAddress)) {
            return null;
        }

        return $sanitisedMacAddress;
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
}