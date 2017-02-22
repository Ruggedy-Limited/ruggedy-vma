<?php

namespace App\Entities;

use App\Contracts\SystemComponent;
use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entities\Comment
 *
 * @ORM\Entity(repositoryClass="App\Repositories\CommentRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Comment extends Base\Comment implements SystemComponent
{
    /**
     * @inheritdoc
     * @return Base\User
     */
    public function getParent()
    {
        return $this->getUser();
    }
}