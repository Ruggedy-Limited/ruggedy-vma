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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="comments", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(name="`user_id`", referencedColumnName="`id`", nullable=false)
     */
    protected $user;

    /**
     * @inheritdoc
     * @return Base\File
     */
    public function getParent()
    {
        return $this->file;
    }
}