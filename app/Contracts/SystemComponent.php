<?php

namespace App\Contracts;

use App\Entities\Base\AbstractEntity;
use App\Entities\Base\User;

/**
 * An Interface for Entities that are owned by a User, indicated by a $user Entity member, and have the related
 * getUser() getter method
 */
interface SystemComponent
{
    /**
     * Get the ID of the Component
     *
     * @return int
     */
    public function getId();

    /**
     * Get the owning User entity
     *
     * @return User
     */
    public function getUser();

    /**
     * Set the owning User entity
     *
     * @param User $user
     */
    public function setUser(User $user);

    /**
     * Get the parent Entity of this one
     *
     * @return SystemComponent|null
     */
    public function getParent();
}