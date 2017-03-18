<?php

namespace App\Commands;

use App\Entities\File;
use Illuminate\Http\UploadedFile;

class UploadScanOutput extends Command
{
    /** @var int */
    protected $id;

    /** @var UploadedFile */
    protected $uploadedFile;

    /** @var File */
    protected $file;

    /**
     * UploadScanOutputCommand constructor.
     *
     * @param int $id
     * @param UploadedFile $uploadedFile
     * @param File $file
     */
    public function __construct(int $id, UploadedFile $uploadedFile, File $file)
    {
        $this->id           = $id;
        $this->uploadedFile = $uploadedFile;
        $this->file         = $file;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return UploadedFile
     */
    public function getUploadedFile()
    {
        return $this->uploadedFile;
    }

    /**
     * @return File
     */
    public function getFile(): File
    {
        return $this->file;
    }
}