<?php

namespace App\Contracts;

use App\Entities\File;

interface RelatesToFiles
{
    /**
     * Add a file relation to this entity
     *
     * @param File $file
     * @return HasIdColumn
     */
    public function addFile(File $file);

    /**
     * Remove a file relation from this entity
     *
     * @param File $file
     * @return HasIdColumn
     */
    public function removeFile(File $file);
}