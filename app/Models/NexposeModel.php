<?php

namespace App\Models;

use App\Contracts\CollectsPortInformation;
use App\Contracts\CollectsScanOutput;
use App\Entities\Asset;
use Illuminate\Support\Collection;

class NexposeModel extends AbstractXmlModel implements CollectsScanOutput, CollectsPortInformation
{
    /**
     * "Microsoft" ends up in here for Microsoft servers, but the Linux distro, e.g. "Ubuntu" and not "Linux" gets
     * captured here for Linux servers and "Linux" gets captured in $this->osFamily, so we have to capture both
     *
     * @var string
     */
    protected $osVendor;

    /**
     * "Linux" ends up in here for Linux servers, but "Windows" and not "Microsoft" gets captured here for Microsoft
     * servers and the Linux distro, e.g. "Ubuntu" and not "Linux gets capture in $this->osVendor, so we have to capture
     * both
     *
     * @var string
     */
    protected $osFamily;

    /** @var string */
    protected $osVersion;

    /** @var string */
    protected $osProduct;

    /** @var string */
    protected $cpe;

    /** @var string */
    protected $macAddress;

    /** @var string */
    protected $netbiosName;

    /**
     * NexposeModel constructor.
     */
    public function __construct()
    {
        // Call the parent constructor
        parent::__construct();

        // Set Asset export mappings for additional asset-related data
        $this->exportForAssetMap->put(Asset::MAC_ADDRESS, 'getMacAddress');
        $this->exportForAssetMap->put(Asset::VENDOR, 'getOsVendor');
        $this->exportForAssetMap->put(Asset::OS_VERSION, 'getOsVersion');
        $this->exportForAssetMap->put(Asset::NETBIOS, 'getNetbiosName');

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
     * Returns a null coalesce of $this->osVendor and $this->osFamily or null
     *
     * @return string|null
     */
    public function getOsVendor()
    {
        return $this->osVendor ?? $this->osFamily ?? null;
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
    public function getOsFamily()
    {
        return $this->osFamily;
    }

    /**
     * @param string $osFamily
     */
    public function setOsFamily(string $osFamily)
    {
        $this->osFamily = $osFamily;
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

        // Split the string into an array where each elements contains two characters and create a Collection
        $macAddressChars = new Collection(
            str_split($macAddress, 2)
        );

        // Implode the Collection with a colon as glue
        $sanitisedMacAddress = $macAddressChars->implode(":");

        // Validate the sanitised MAC address against the regex
        if (!preg_match(Asset::REGEX_MAC_ADDRESS, $sanitisedMacAddress)) {
            return null;
        }

        return $sanitisedMacAddress;
    }

    /**
     * @return string
     */
    public function getNetbiosName()
    {
        return $this->netbiosName;
    }

    /**
     * @param string $netbiosName
     */
    public function setNetbiosName(string $netbiosName)
    {
        $this->netbiosName = $netbiosName;
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