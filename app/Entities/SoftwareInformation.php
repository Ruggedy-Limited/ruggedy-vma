<?php

namespace App\Entities;

use App\Contracts\HasIdColumn;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entities\SoftwareInformation
 *
 * @ORM\Entity(repositoryClass="App\Repositories\SoftwareInformationRepository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="`software_information`")
 */
class SoftwareInformation extends Base\SoftwareInformation implements HasIdColumn
{
    /**
     * @ORM\ManyToMany(targetEntity="File", mappedBy="software_information")
     */
    protected $files;

    /**
     * SoftwareInformation constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->files = new ArrayCollection();
    }

    /**
     * @param File $file
     * @return $this
     */
    public function addFile(File $file)
    {
        $this->files[] = $file;

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
}