<?php

namespace App\Entities;

use App\Contracts\HasComponentPermissions;
use App\Contracts\SystemComponent;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;
use stdClass;

/**
 * App\Entities\User
 *
 * @ORM\Entity(repositoryClass="App\Repositories\UserRepository")
 * @ORM\HasLifecycleCallbacks
 */
class User extends Base\User implements Authenticatable, AuthorizableContract, SystemComponent, HasComponentPermissions
{
    use Authorizable;

    /**
     * Override the AbstractEntity method, just to provide a default set of attributes to include when coercing to
     * stdClass for JSON
     *
     * @param array $onlyTheseAttributes
     * @return stdClass
     */
    public function toStdClass($onlyTheseAttributes = [])
    {
        // Set a list of attributes to include by default when no specific list is given
        if (empty($onlyTheseAttributes)) {
            $onlyTheseAttributes = [
                'id', 'name', 'email', 'photo_url', 'uses_two_factor_auth', 'created_at', 'updated_at'
            ];
        }

        return parent::toStdClass($onlyTheseAttributes);
    }

    /**
     * A more sensible alias for the generated Entity's getComponentPermissionRelatedByUserIds() method
     *
     * @return Collection
     */
    public function getPermissions(): Collection
    {
        return parent::getComponentPermissionRelatedByUserIds();
    }

    /**
     * Returns itself when authorising changes to this User account
     * 
     * @return $this
     */
    public function getUser()
    {
        return $this;
    }

    /**
     * Does nothing, but required to implement the SystemComponent contract
     *
     * @param Base\User $user
     * @return $this
     */
    public function setUser(Base\User $user)
    {
        return $this;
    }

    /**
     * Check if the User is an admin
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return true; //TODO: Implement this properly as an additional column in the users table
    }

    /**
     * Get the parent Entity of this Entity
     *
     * @return User
     */
    public function getParent()
    {
        return $this;
    }

    /**
     * @inheritdoc
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'id';
    }

    /**
     * @inheritdoc
     * @return int
     */
    public function getAuthIdentifier()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * @inheritdoc
     * @return string
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
    }
}