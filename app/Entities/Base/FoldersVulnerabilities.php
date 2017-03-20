<?php

namespace App\Entities\Base;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entities\Base\FoldersVulnerabilities
 *
 * @ORM\MappedSuperclass
 * @ORM\Table(name="`folders_vulnerabilities`", indexes={
 *     @ORM\Index(name="folders_vulnerabilities_vulnerability_fk_idx", columns={"`vulnerability_id`"}),
 *     @ORM\Index(name="folders_vulnerabilities_file_fk_idx", columns={"`file_id`"}),
 *     @ORM\Index(name="folders_vulnerabilities_folder_fk_idx", columns={"`folder_id`"
 * })})
 */
class FoldersVulnerabilities extends AbstractEntity
{
    /** Table name constant */
    const TABLE_NAME = 'folders_vulnerabilities';

    /** Column name constants */
    const ID = 'id';
    const FOLDER_ID = 'folder_id';
    const VULNERABILITY_ID = 'vulnerability_id';
    const FILE_ID = 'file_id';
    const FOLDER = 'folder';
    const VULNERABILITY = 'vulnerability';
    const FILE = 'file';

    /**
     * @ORM\Id
     * @ORM\Column(name="`id`", type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="`folder_id`", type="integer", options={"unsigned":true})
     */
    protected $folder_id;

    /**
     * @ORM\Column(name="`vulnerability_id`", type="integer", options={"unsigned":true})
     */
    protected $vulnerability_id;

    /**
     * @ORM\Column(name="`file_id`", type="integer", options={"unsigned":true})
     */
    protected $file_id;

    /**
     * @ORM\ManyToOne(targetEntity="Folder", inversedBy="foldersVulnerabilities", cascade={"persist"})
     * @ORM\JoinColumn(name="`folder_id`", referencedColumnName="`id`", nullable=false)
     */
    protected $folder;

    /**
     * @ORM\ManyToOne(targetEntity="Vulnerability", inversedBy="foldersVulnerabilities", cascade={"persist"})
     * @ORM\JoinColumn(name="`vulnerability_id`", referencedColumnName="`id`", nullable=false)
     */
    protected $vulnerability;

    /**
     * @ORM\ManyToOne(targetEntity="File", inversedBy="foldersVulnerabilities", cascade={"persist"})
     * @ORM\JoinColumn(name="`file_id`", referencedColumnName="`id`", nullable=false)
     */
    protected $file;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return FoldersVulnerabilities
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFolderId()
    {
        return $this->folder_id;
    }

    /**
     * @param mixed $folder_id
     * @return FoldersVulnerabilities
     */
    public function setFolderId($folder_id)
    {
        $this->folder_id = $folder_id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getVulnerabilityId()
    {
        return $this->vulnerability_id;
    }

    /**
     * @param mixed $vulnerability_id
     * @return FoldersVulnerabilities
     */
    public function setVulnerabilityId($vulnerability_id)
    {
        $this->vulnerability_id = $vulnerability_id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFileId()
    {
        return $this->file_id;
    }

    /**
     * @param mixed $file_id
     * @return FoldersVulnerabilities
     */
    public function setFileId($file_id)
    {
        $this->file_id = $file_id;

        return $this;
    }

    /**
     * @return Folder
     */
    public function getFolder()
    {
        return $this->folder;
    }

    /**
     * @param Folder $folder
     * @return FoldersVulnerabilities
     */
    public function setFolder(Folder $folder)
    {
        $folder->addFoldersVulnerabilities($this);
        $this->folder = $folder;

        return $this;
    }

    /**
     * @return Vulnerability
     */
    public function getVulnerability()
    {
        return $this->vulnerability;
    }

    /**
     * @param Vulnerability $vulnerability
     * @return FoldersVulnerabilities
     */
    public function setVulnerability(Vulnerability $vulnerability)
    {
        $vulnerability->addFoldersVulnerabilities($this);
        $this->vulnerability = $vulnerability;

        return $this;
    }

    /**
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param File $file
     * @return FoldersVulnerabilities
     */
    public function setFile(File $file)
    {
        $file->addFoldersVulnerabilities($this);
        $this->file = $file;

        return $this;
    }

    public function __sleep()
    {
        return array('id', 'folder_id', 'vulnerability_id', 'file_id', 'created_at');
    }
}