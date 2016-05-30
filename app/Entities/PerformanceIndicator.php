<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entities\PerformanceIndicator
 *
 * @ORM\Entity(repositoryClass="App\Repositories\PerformanceIndicatorRepository")
 * @ORM\HasLifecycleCallbacks
 */
class PerformanceIndicator extends Base\PerformanceIndicator
{
}