<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entities\Migration
 *
 * @ORM\Entity(repositoryClass="App\Repositories\MigrationRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Migration extends Base\Migration
{
}