<?php


namespace App\Commands;

use Illuminate\Http\UploadedFile;

class UploadScanOutput extends CreateSomething
{
    /** @var UploadedFile */
    protected $file;

    /**
     * UploadScanOutputCommand constructor.
     *
     * @param int $id
     * @param array $details
     * @param UploadedFile $file
     */
    public function __construct($id, array $details, UploadedFile $file)
    {
        parent::__construct($id, $details);
        $this->file = $file;
    }

    /**
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }
}