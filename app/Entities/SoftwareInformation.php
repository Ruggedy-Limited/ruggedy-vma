<?php

namespace App\Entities;

use App\Contracts\GeneratesUniqueHash;
use App\Contracts\HasIdColumn;
use App\Contracts\RelatesToFiles;
use App\Entities\Base\AbstractEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Illuminate\Support\Collection;

/**
 * App\Entities\SoftwareInformation
 *
 * @ORM\Entity(repositoryClass="App\Repositories\SoftwareInformationRepository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="`software_information`")
 */
class SoftwareInformation extends Base\SoftwareInformation implements HasIdColumn, RelatesToFiles, GeneratesUniqueHash
{
    /**
     * @ORM\ManyToMany(targetEntity="File", mappedBy="softwareInformation", indexBy="id")
     */
    protected $files;

    /**
     * @ORM\ManyToMany(targetEntity="Asset", mappedBy="softwareInformation")
     */
    protected $assets;

    /**
     * SoftwareInformation constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->files  = new ArrayCollection();
        $this->assets = new ArrayCollection();
    }

    /**
     * @param File $file
     * @return $this
     */
    public function addFile(File $file)
    {
        $this->files[$file->getId()] = $file;

        return $this;
    }

    /**
     * @param File $file
     * @return $this
     */
    public function removeFile(File $file)
    {
        $this->files->removeElement($file);

        return $this;
    }

    /**
     * Add an Asset relation
     *
     * @param Asset $asset
     * @return $this
     */
    public function addAsset(Asset $asset)
    {
        $this->assets[$asset->getId()] = $asset;

        return $this;
    }

    /**
     * Remove an Asset relation
     *
     * @param Asset $asset
     * @return $this
     */
    public function removeAsset(Asset $asset)
    {
        $this->assets->removeElement($asset);

        return $this;
    }

    /**
     * Convenience method for setting the parent Asset entity
     *
     * @param Asset $asset
     * @return Asset
     */
    public function setParent(Asset $asset)
    {
        return $this->addAsset($asset);
    }

    /**
     * @inheritdoc
     * @return string
     */
    public function getHash(): string
    {
        return AbstractEntity::generateUniqueHash($this->getUniqueKeyColumns());
    }

    /**
     * @inheritdoc
     * @return Collection
     */
    public function getUniqueKeyColumns(): Collection
    {
        return collect([
            parent::NAME    => $this->name,
            parent::VERSION => $this->version,
            parent::VENDOR  => $this->vendor,
        ]);
    }
}