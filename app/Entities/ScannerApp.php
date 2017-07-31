<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;
use Illuminate\Support\Collection;

/**
 * App\Entities\ScannerApp
 *
 * @ORM\Entity(repositoryClass="App\Repositories\ScannerAppRepository")
 * @ORM\HasLifecycleCallbacks
 */
class ScannerApp extends Base\ScannerApp
{
    const SCANNER_NMAP       = 'nmap';
    const SCANNER_BURP       = 'burp';
    const SCANNER_NEXPOSE    = 'nexpose';
    const SCANNER_NETSPARKER = 'netsparker';
    const SCANNER_NESSUS     = 'nessus';
    const SCANNER_RUGGEDY    = 'ruggedy';

    /**
     * Get a Collection of available scanner apps
     *
     * @return Collection
     */
    public static function getScannerApps(): Collection
    {
        return collect([
            self::SCANNER_NMAP,
            self::SCANNER_BURP,
            self::SCANNER_NEXPOSE,
            self::SCANNER_NETSPARKER,
            self::SCANNER_NESSUS,
            self::SCANNER_RUGGEDY,
        ]);
    }
}