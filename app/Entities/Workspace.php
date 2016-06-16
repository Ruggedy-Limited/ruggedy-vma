<?php

namespace App\Entities;

use App\Contracts\HasOwnerUserEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entities\Workspace
 *
 * @ORM\Entity(repositoryClass="App\Repositories\WorkspaceRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Workspace extends Base\Workspace implements HasOwnerUserEntity
{
}