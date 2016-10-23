<?php

namespace App\Commands;

use Illuminate\Http\UploadedFile;

class UploadScanOutput extends Command
{
    /** @var int */
    protected $id;

    /** @var UploadedFile */
    protected $file;

    /**
     * UploadScanOutputCommand constructor.
     *
     * @param int $id
     * @param UploadedFile $file
     */
    public function __construct(int $id, UploadedFile $file)
    {
        $this->id   = $id;
        $this->file = $file;
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
    public function getFile()
    {
        return $this->file;
    }
}