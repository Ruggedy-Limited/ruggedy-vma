<?php

namespace App\Commands;

use App\Entities\File;

class ParseFile extends Command
{
    /** @var File */
    protected $file;

    /**
     * ParseFile constructor.
     *
     * @param File $file
     */
    public function __construct(File $file)
    {
        $this->file = $file;
    }

    /**
     * @return File
     */
    public function getFile(): File
    {
        return $this->file;
    }
}