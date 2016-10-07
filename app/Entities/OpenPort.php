<?php

namespace App\Entities;

use App\Contracts\HasIdColumn;
use App\Contracts\RelatesToFiles;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entities\OpenPort
 *
 * @ORM\Entity(repositoryClass="App\Repositories\OpenPortRepository")
 * @ORM\HasLifecycleCallbacks
 */
class OpenPort extends Base\OpenPort implements HasIdColumn, RelatesToFiles
{
    /**
     * @ORM\ManyToMany(targetEntity="File", mappedBy="open_ports", indexBy="id")
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
}