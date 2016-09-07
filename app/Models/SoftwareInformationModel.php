<?php

namespace App\Models;

use App\Entities\SoftwareInformation;
use Illuminate\Support\Collection;

class SoftwareInformationModel
{
    /** @var string */
    protected $name;

    /** @var string */
    protected $vendor;

    /** @var string */
    protected $version;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getVendor()
    {
        return $this->vendor;
    }

    /**
     * @param string $vendor
     */
    public function setVendor(string $vendor)
    {
        $this->vendor = $vendor;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param string $version
     */
    public function setVersion(string $version)
    {
        $this->version = $version;
    }

    /**
     * Get an MD5 hash of the name and version, and the vendor if set.
     * If the name or version are not set, NULL is returned.
     *
     * @return null|string
     */
    public function getHash()
    {
        if (!isset($this->name, $this->version)) {
            return null;
        }

        if (!isset($this->vendor)) {
            return md5($this->name . ":" . $this->version);
        }

        return md5($this->name . ":" . $this->version . ":" . $this->vendor);
    }

    /**
     * Export the software information as an array to be used to populate
     * an instance of a SoftwareInformation Doctrine entity
     *
     * @return Collection
     */
    public function export(): Collection
    {
        return new Collection([
            SoftwareInformation::NAME    => $this->getName(),
            SoftwareInformation::VENDOR  => $this->getVendor(),
            SoftwareInformation::VERSION => $this->getVersion(),
        ]);
    }
}