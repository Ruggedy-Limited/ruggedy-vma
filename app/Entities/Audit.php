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
 * App\Entities\Audit
 *
 * @ORM\Entity(repositoryClass="App\Repositories\AuditRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Audit extends Base\Audit implements GeneratesUniqueHash, RelatesToFiles, HasIdColumn
{
    /**
     * @ORM\ManyToMany(targetEntity="Asset", mappedBy="audits")
     */
    protected $assets;

    /**
     * @ORM\ManyToMany(targetEntity="File", mappedBy="audits")
     */
    protected $files;

    /**
     * Audit constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->assets = new ArrayCollection();
        $this->files  = new ArrayCollection();
    }

    /**
     * @param Asset $asset
     * @return $this
     */
    public function addAsset(Asset $asset)
    {
        $this->assets[$asset->getId()] = $asset;

        return $this;
    }

    /**
     * @param Asset $asset
     * @return $this
     */
    public function removeAsset(Asset $asset)
    {
        $this->assets->removeElement($asset);

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getAssets()
    {
        return $this->assets;
    }

    /**
     * Add a file relation
     *
     * @param File $file
     * @return $this
     */
    public function addFile(File $file)
    {
        $this->files[$file->getId()] = $file;

        return $this;
    }

    /**
     * Remove a file relation
     *
     * @param File $file
     * @return $this
     */
    public function removeFile(File $file)
    {
        $this->files->removeElement($file);

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getFiles()
    {
        return $this->files;
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
            parent::COMPLIANCE_CHECK_ID   => $this->compliance_check_id,
            parent::COMPLIANCE_CHECK_NAME => $this->compliance_check_name,
            parent::AUDIT_FILE            => $this->audit_file,
        ]);
    }
}