<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * App\Entities\Project
 *
 * @ORM\Entity(repositoryClass="App\Repositories\ProjectRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Project extends Base\Project
{
}