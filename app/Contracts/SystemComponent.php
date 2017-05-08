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

    /**
     * Get the display name for the entity
     *
     * @param bool $plural
     * @return string
     */
    public function getDisplayName(bool $plural = false): string;

    /**
     * Get the name used in routes related to this entity
     *
     * @param bool $plural
     * @return string
     */
    public function getRouteName(bool $plural = false): string;

    /**
     * Get the route parameter name used for passing the ID of a specific entity to a route
     *
     * @return string
     */
    public function getRouteParameterName(): string;
}