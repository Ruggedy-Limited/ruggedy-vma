<?php

namespace App\Contracts;

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
    function getId();

    /**
     * Get the owning User entity
     *
     * @return User
     */
    function getUser();

    /**
     * Get the parent Entity of this one
     *
     * @return SystemComponent|null
     */
    function getParent();
}