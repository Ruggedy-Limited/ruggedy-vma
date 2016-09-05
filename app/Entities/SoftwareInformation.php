<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entities\SoftwareInformation
 *
 * @ORM\Entity(repositoryClass="App\Repositories\SoftwareInformationRepository")
 * @ORM\HasLifecycleCallbacks
 */
class SoftwareInformation extends Base\SoftwareInformation
{
}