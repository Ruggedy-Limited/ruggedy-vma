<?php

namespace App\Contracts;

use App\Entities\Base\User;


/**
 * An Interface for Entities that are owned by a User, indicated by a $user Entity member, and have the related
 * getUser() getter method
 */
interface HasOwnerUserEntity
{
    /**
     * Get the owning User entity
     *
     * @return User
     */
    function getUser();
}