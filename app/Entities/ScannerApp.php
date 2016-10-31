<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;

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
}