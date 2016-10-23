<?php

namespace App\Entities;

use App\Contracts\SystemComponent;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Illuminate\Support\Collection;

/**
 * App\Entities\File
 *
 * @ORM\Entity(repositoryClass="App\Repositories\FileRepository")
 * @ORM\HasLifecycleCallbacks
 */
class File extends Base\File implements SystemComponent
{
    const FILE_TYPE_XML  = 'xml';
    const FILE_TYPE_CSV  = 'csv';
    const FILE_TYPE_JSON = 'json';

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="files", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(name="`user_id`", referencedColumnName="`id`", nullable=false)
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Workspace", inversedBy="files", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(name="`workspace_id`", referencedColumnName="`id`", nullable=false)
     */
    protected $workspace;

    /**
     * @ORM\ManyToMany(targetEntity="Asset", inversedBy="files", indexBy="id")
     * @ORM\JoinTable(name="files_assets")
     */
    protected $assets;

    /**
     * @ORM\ManyToMany(targetEntity="Vulnerability", inversedBy="files", indexBy="name")
     * @ORM\JoinTable(name="files_vulnerabilities")
     */
    protected $vulnerabilities;

    /**
     * @ORM\ManyToMany(targetEntity="OpenPort", inversedBy="files", indexBy="number")
     * @ORM\JoinTable(name="files_open_ports")
     */
    protected $openPorts;

    /**
     * @ORM\ManyToMany(targetEntity="SoftwareInformation", inversedBy="files", indexBy="name")
     * @ORM\JoinTable(name="files_software_information")
     */
    protected $softwareInformation;

    /**
     * File constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->assets                      = new ArrayCollection();
        $this->vulnerabilities             = new ArrayCollection();
        $this->openPorts                   = new ArrayCollection();
        $this->softwareInformation         = new ArrayCollection();
    }

    /**
     * Get a Collection of valid file types
     *
     * @return Collection
     */
    public static function getValidFileTypes()
    {
        return new Collection([
            static::FILE_TYPE_XML,
            static::FILE_TYPE_CSV,
            static::FILE_TYPE_JSON,
        ]);
    }

    /**
     * Check if the given file type is valid
     *
     * @param string $fileType
     * @return bool
     */
    public static function isValidFileType(string $fileType)
    {
        return static::getValidFileTypes()->contains($fileType);
    }

    /**
     * @inheritdoc
     * @return Base\User
     */
    public function getParent()
    {
        return $this->getUser();
    }

    /**
     * @param Asset $asset
     */
    public function addAsset(Asset $asset)
    {
        $asset->addFile($this); // synchronously updating inverse side
        $relationKey = $asset->getId() ?? $asset->getHash();
        $this->assets[$relationKey] = $asset;
    }

    /**
     * @param Asset $asset
     */
    public function removeAsset(Asset $asset)
    {
        $asset->removeFile($this); // synchronously updating inverse side
        $this->assets->removeElement($asset);
    }

    /**
     * @param Vulnerability $vulnerability
     */
    public function addVulnerability(Vulnerability $vulnerability)
    {
        $vulnerability->addFile($this); // synchronously updating inverse side
        $relationKey = $vulnerability->getId() ?? $vulnerability->getHash();
        $this->vulnerabilities[$relationKey] = $vulnerability;
    }

    /**
     * @param Vulnerability $vulnerability
     */
    public function removeVulnerability(Vulnerability $vulnerability)
    {
        $vulnerability->removeFile($this); // synchronously updating inverse side
        $this->vulnerabilities->removeElement($vulnerability);
    }

    /**
     * @param OpenPort $openPort
     */
    public function addOpenPort(OpenPort $openPort)
    {
        $openPort->addFile($this); // synchronously updating inverse side
        $relationKey = $openPort->getId() ?? $openPort->getHash();
        $this->openPorts[$relationKey] = $openPort;
    }

    /**
     * @param OpenPort $openPort
     */
    public function removeOpenPort(OpenPort $openPort)
    {
        $openPort->removeFile($this); // synchronously updating inverse side
        $this->openPorts->removeElement($openPort);
    }

    /**
     * @param SoftwareInformation $softwareInformation
     */
    public function addSoftwareInformation(SoftwareInformation $softwareInformation)
    {
        $softwareInformation->addFile($this); // synchronously updating inverse side
        $relationKey = $softwareInformation->getId() ?? $softwareInformation->getHash();
        $this->softwareInformation[$relationKey] = $softwareInformation;
    }

    /**
     * @param SoftwareInformation $softwareInformation
     */
    public function removeSoftwareInformation(SoftwareInformation $softwareInformation)
    {
        $softwareInformation->removeFile($this);
        $this->softwareInformation->removeElement($softwareInformation);
    }
}