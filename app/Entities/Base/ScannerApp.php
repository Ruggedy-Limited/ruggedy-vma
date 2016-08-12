<?php

namespace App\Entities\Base;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * App\Entities\Base\ScannerApp
 *
 * @ORM\MappedSuperclass
 * @ORM\Table(name="`scanner_apps`")
 */
class ScannerApp extends AbstractEntity
{
    /** Table name constant */
    const TABLE_NAME = 'scanner_apps';

    /** Column name constants */
    const NAME        = 'name';
    const DESCRIPTION = 'description';
    const FILES       = 'files';

    /**
     * @ORM\Id
     * @ORM\Column(name="`id`", type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="`name`", type="string", length=45)
     */
    protected $name;

    /**
     * @ORM\Column(name="`description`", type="string", length=255, nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(name="`created_at`", type="datetime")
     */
    protected $created_at;

    /**
     * @ORM\Column(name="`updated_at`", type="datetime")
     */
    protected $updated_at;

    /**
     * @ORM\OneToMany(targetEntity="File", mappedBy="scannerApp", cascade={"persist"})
     * @ORM\JoinColumn(name="`id`", referencedColumnName="`scanner_app_id`", nullable=false)
     */
    protected $files;

    public function __construct()
    {
        $this->files = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entities\Base\ScannerApp
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of name.
     *
     * @param string $name
     * @return \App\Entities\Base\ScannerApp
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of description.
     *
     * @param string $description
     * @return \App\Entities\Base\ScannerApp
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the value of description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the value of created_at.
     *
     * @param \DateTime $created_at
     * @return \App\Entities\Base\ScannerApp
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * Get the value of created_at.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set the value of updated_at.
     *
     * @param \DateTime $updated_at
     * @return \App\Entities\Base\ScannerApp
     */
    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * Get the value of updated_at.
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Add File entity to collection (one to many).
     *
     * @param \App\Entities\Base\File $file
     * @return \App\Entities\Base\ScannerApp
     */
    public function addFile(File $file)
    {
        $this->files[] = $file;

        return $this;
    }

    /**
     * Remove File entity from collection (one to many).
     *
     * @param \App\Entities\Base\File $file
     * @return \App\Entities\Base\ScannerApp
     */
    public function removeFile(File $file)
    {
        $this->files->removeElement($file);

        return $this;
    }

    /**
     * Get File entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFiles()
    {
        return $this->files;
    }

    public function __sleep()
    {
        return array('id', 'name', 'description', 'created_at', 'updated_at');
    }
}