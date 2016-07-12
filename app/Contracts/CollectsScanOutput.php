<?php

namespace App\Contracts;

use Illuminate\Support\Collection;

interface CollectsScanOutput
{
    /**
     * Get the Asset related data as a Collection
     * 
     * @return Collection
     */
    function exportForAsset(): Collection;
}