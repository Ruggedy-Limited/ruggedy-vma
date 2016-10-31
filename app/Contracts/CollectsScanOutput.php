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
    public function exportForAsset(): Collection;

    /**
     * Get the Vulnerability related data as a Collection
     *
     * @return Collection
     */
    public function exportForVulnerability(): Collection;

    /**
     * Export multiple Vulnerabilities as Collection
     *
     * @return Collection
     */
    public function exportForVulnerabilities(): Collection;

    /**
     * Get the Vulnerability Reference related data as a Collection
     *
     * @return Collection
     */
    public function exportForVulnerabilityReference(): Collection;

    /**
     * Get the System Information related data as a Collection
     *
     * @return Collection
     */
    public function exportOpenPorts(): Collection;

    /**
     * Get the Software Information data as a Collection
     *
     * @return Collection
     */
    public function exportSoftwareInformation(): Collection;
}