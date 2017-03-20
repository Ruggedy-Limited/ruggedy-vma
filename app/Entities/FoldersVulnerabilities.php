<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entities\FoldersVulnerabilities
 *
 * @ORM\Entity(repositoryClass="App\Repositories\FoldersVulnerabilitiesRepository")
 * @ORM\HasLifecycleCallbacks
 */
class FoldersVulnerabilities extends Base\FoldersVulnerabilities
{
}