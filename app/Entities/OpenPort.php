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
 * App\Entities\OpenPort
 *
 * @ORM\Entity(repositoryClass="App\Repositories\OpenPortRepository")
 * @ORM\HasLifecycleCallbacks
 */
class OpenPort extends Base\OpenPort implements HasIdColumn, RelatesToFiles, GeneratesUniqueHash
{
    /**
     * @ORM\ManyToMany(targetEntity="File", mappedBy="open_ports")
     */
    protected $files;

    /**
     * OpenPort constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->files = new ArrayCollection();
    }

    /**
     * Override the parent setter to always uppercase the input before setting the property with the value
     *
     * @param string $protocol
     * @return Base\OpenPort
     */
    public function setProtocol($protocol)
    {
        return parent::setProtocol(
            strtoupper($protocol)
        );
    }

    /**
     * Override the parent setter to always uppercase the input before setting the property with the value
     *
     * @param string $service_name
     * @return Base\OpenPort
     */
    public function setServiceName($service_name)
    {
        return parent::setServiceName(
            strtoupper($service_name)
        );
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
     * Convenience method for returning the parent Asset entity
     *
     * @return mixed
     */
    public function getParent()
    {
        return $this->asset;
    }

    /**
     * Convenience method for setting the parent Asset entity
     *
     * @param Base\Asset $asset
     * @return Base\OpenPort
     */
    public function setParent(Base\Asset $asset)
    {
        return parent::setAsset($asset);
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
            parent::NUMBER => $this->number,
            parent::ASSET  => $this->asset,
        ]);
    }
}