<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entities\SystemInformation
 *
 * @ORM\Entity(repositoryClass="App\Repositories\SystemInformationRepository")
 * @ORM\HasLifecycleCallbacks
 */
class SystemInformation extends Base\SystemInformation
{
}