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
    const FILE_TYPE_XML    = 'xml';
    const FILE_TYPE_CSV    = 'csv';
    const FILE_TYPE_JSON   = 'json';
    const FILE_TYPE_STREAM = 'octet-stream';

    const FILE_EXTENSION_NESSUS = 'nessus';

    /**
     * @ORM\OneToMany(targetEntity="Vulnerability", mappedBy="file", cascade={"persist"}, fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"severity" = "DESC"})
     * @ORM\JoinColumn(name="`id`", referencedColumnName="`file_id`", nullable=false, onDelete="CASCADE")
     */
    protected $vulnerabilities;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="files", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(name="`user_id`", referencedColumnName="`id`", nullable=false)
     */
    protected $user;

    /**
     * @ORM\ManyToMany(targetEntity="OpenPort", inversedBy="files", fetch="EXTRA_LAZY")
     * @ORM\JoinTable(name="files_open_ports",
     *     joinColumns={@ORM\JoinColumn(name="file_id", referencedColumnName="id", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="open_port_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    protected $openPorts;

    /**
     * @ORM\ManyToMany(targetEntity="SoftwareInformation", inversedBy="files", fetch="EXTRA_LAZY")
     * @ORM\JoinTable(name="files_software_information",
     *     joinColumns={@ORM\JoinColumn(name="file_id", referencedColumnName="id", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="software_information_id", referencedColumnName="id",
     *     onDelete="CASCADE")}
     * )
     */
    protected $softwareInformation;

    /**
     * @ORM\ManyToMany(targetEntity="Audit", inversedBy="files", fetch="EXTRA_LAZY")
     * @ORM\JoinTable(name="files_audits",
     *     joinColumns={@ORM\JoinColumn(name="file_id", referencedColumnName="id", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="audit_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    protected $audits;

    /**
     * File constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->openPorts           = new ArrayCollection();
        $this->softwareInformation = new ArrayCollection();
        $this->audits              = new ArrayCollection();
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
            static::FILE_TYPE_STREAM
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
     * @return Base\WorkspaceApp
     */
    public function getParent()
    {
        return $this->workspaceApp;
    }

    /**
     * @param OpenPort $openPort
     * @return $this
     */
    public function addOpenPort(OpenPort $openPort)
    {
        if ($this->openPorts->contains($openPort)) {
            return $this;
        }

        $openPort->addFile($this); // synchronously updating inverse side
        $relationKey = $openPort->getId() ?? $openPort->getHash();
        $this->openPorts[$relationKey] = $openPort;

        return $this;
    }

    /**
     * @param OpenPort $openPort
     * @return $this
     */
    public function removeOpenPort(OpenPort $openPort)
    {
        $openPort->removeFile($this); // synchronously updating inverse side
        $this->openPorts->removeElement($openPort);

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getOpenPorts()
    {
        return $this->openPorts;
    }

    /**
     * @param SoftwareInformation $softwareInformation
     * @return $this
     */
    public function addSoftwareInformation(SoftwareInformation $softwareInformation)
    {
        if ($this->softwareInformation->contains($softwareInformation)) {
            return $this;
        }

        $softwareInformation->addFile($this); // synchronously updating inverse side
        $relationKey = $softwareInformation->getId() ?? $softwareInformation->getHash();
        $this->softwareInformation[$relationKey] = $softwareInformation;

        return $this;
    }

    /**
     * @param SoftwareInformation $softwareInformation
     * @return $this
     */
    public function removeSoftwareInformation(SoftwareInformation $softwareInformation)
    {
        $softwareInformation->removeFile($this);
        $this->softwareInformation->removeElement($softwareInformation);

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getSoftwareInformation()
    {
        return $this->softwareInformation;
    }

    /**
     * @param Audit $audit
     * @return $this
     */
    public function addAudit(Audit $audit)
    {
        if ($this->audits->contains($audit)) {
            return $this;
        }

        $audit->addFile($this); // synchronously updating inverse side
        $relationKey = $audit->getId() ?? $audit->getHash();
        $this->audits[$relationKey] = $audit;

        return $this;
    }

    /**
     * @param Audit $audit
     * @return $this
     */
    public function removeAudit(Audit $audit)
    {
        $audit->removeFile($this);
        $this->audits->removeElement($audit);

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getAudits()
    {
        return $this->audits;
    }
}