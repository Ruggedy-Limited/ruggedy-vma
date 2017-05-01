<?php

namespace App\Commands;

use App\Entities\User;

class ResetPassword extends Command
{
    /** @var User */
    protected $user;

    /**
     * ResetPassword constructor.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
}