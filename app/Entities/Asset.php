<?php

namespace App\Entities;

use App\Contracts\HasGetId;
use App\Contracts\HasOwnerUserEntity;
use Doctrine\ORM\Mapping as ORM;


/**
 * App\Entities\Asset
 *
 * @ORM\Entity(repositoryClass="App\Repositories\AssetRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Asset extends Base\Asset implements HasGetId, HasOwnerUserEntity
{
}