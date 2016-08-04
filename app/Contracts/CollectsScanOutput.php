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

    /**
     * Get the Vulnerability related data as a Collection
     *
     * @return Collection
     */
    function exportForVulnerability(): Collection;

    /**
     * Get the Vulnerability Reference related data as a Collection
     *
     * @return Collection
     */
    function exportForVulnerabilityReference(): Collection;

    /**
     * Get the System Information related data as a Collection
     *
     * @return Collection
     */
    function exportForOpenPort(): Collection;
}