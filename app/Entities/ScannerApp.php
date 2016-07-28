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
}