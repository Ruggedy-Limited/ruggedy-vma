<?php

namespace App\Contracts;

use Illuminate\Support\Collection;

interface ParsesXmlFiles
{
    /**
     * Get the mapping of fields from the file being parsed to the columns in the database
     * Takes an optional Entity class to extract data for a specific entity
     *
     * @return Collection
     */
    public function getFileToSchemaMapping(): Collection;
}