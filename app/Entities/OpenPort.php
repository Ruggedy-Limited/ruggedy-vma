<?php

namespace App\Entities;

use App\Contracts\HasIdColumn;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entities\OpenPort
 *
 * @ORM\Entity(repositoryClass="App\Repositories\OpenPortRepository")
 * @ORM\HasLifecycleCallbacks
 */
class OpenPort extends Base\OpenPort implements HasIdColumn
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
     * @return Base\User
     */
    function getUser()
    {
        return $this->getFile()->getUser();
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