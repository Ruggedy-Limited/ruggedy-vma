<?php

namespace App\Entities;

use App\Contracts\SystemComponent;
use Auth;
use Carbon\Carbon;
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
        return $this->vulnerability->getFile();
    }

	/**
	 * Get a human-readable representation of the time since the comment was posted
	 *
	 * @return string
	 */
    public function getTimeSinceComment()
    {
    	return Carbon::instance($this->created_at)->diffForHumans();
    }

	/**
	 * Check if the comment is editable by the authenticated User. Only the comment author can edit a comment.
	 *
	 * @return bool
	 */
	public function isEditable(): bool
	{
		return Auth::user() && Auth::user()->getId() === $this->user->getId();
	}

	/**
	 * Check if a comment can be deleted by the authenticated User. Comment authors and admins can delete comments.
	 *
	 * @return bool
	 */
	public function isDeletable(): bool
	{
		return Auth::user() && (Auth::user()->getId() === $this->user->getId() || Auth::user()->isAdmin());
	}
}