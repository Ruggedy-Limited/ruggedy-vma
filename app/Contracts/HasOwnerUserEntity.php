<?php

namespace App\Contracts;

use App\Entities\Base\User;


interface HasOwnerUserEntity
{
    /**
     * Get the owning User entity
     *
     * @return User
     */
    function getUser();
}