<?php

namespace App\Entities;

use App\Contracts\SystemComponent;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entities\Folder
 *
 * @ORM\Entity(repositoryClass="App\Repositories\FolderRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Folder extends Base\Folder implements SystemComponent
{
    /** Related Entity constants */
    const VULNERABILITIES = 'vulnerabilities';

    /**
     * @ORM\ManyToMany(targetEntity="Vulnerability", inversedBy="folders", cascade={"persist", "remove"},
     *     fetch="EXTRA_LAZY")
     * @ORM\JoinTable(
     *     name="folders_vulnerabilities",
     *     joinColumns={@ORM\JoinColumn(name="folder_id", referencedColumnName="id", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="vulnerability_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    protected $vulnerabilities;

    /**
     * Folder constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->vulnerabilities = new ArrayCollection();
    }

    /**
     * @inheritdoc
     * @return Base\Workspace
     */
    public function getParent()
    {
        return $this->getWorkspace();
    }

    /**
     * @param Vulnerability $vulnerability
     * @return $this
     */
    public function addVulnerability(Vulnerability $vulnerability)
    {
        if ($this->vulnerabilities->contains($vulnerability)) {
            return $this;
        }

        $vulnerability->addFolder($this); // synchronously updating inverse side
        $relationKey = $vulnerability->getId() ?? $vulnerability->getHash();
        $this->vulnerabilities[$relationKey] = $vulnerability;

        return $this;
    }

    /**
     * @param Vulnerability $vulnerability
     * @return $this
     */
    public function removeVulnerability(Vulnerability $vulnerability)
    {
        $vulnerability->removeFolder($this); // synchronously updating inverse side
        $this->vulnerabilities->removeElement($vulnerability);

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getVulnerabilities()
    {
        return $this->vulnerabilities;
    }
}