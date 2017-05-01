<?php

namespace App\Entities;

use App\Contracts\HasComponentPermissions;
use App\Contracts\SystemComponent;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordsTrait;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use LaravelDoctrine\ORM\Notifications\Notifiable;
use stdClass;

/**
 * App\Entities\User
 *
 * @ORM\Entity(repositoryClass="App\Repositories\UserRepository")
 * @ORM\HasLifecycleCallbacks
 */
class User extends Base\User implements Authenticatable, AuthorizableContract, SystemComponent, HasComponentPermissions,
    CanResetPassword
{
    use Authorizable, CanResetPasswordsTrait, Notifiable;

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
                'id', 'name', 'email', 'created_at', 'updated_at'
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
        $name = $this->getAuthIdentifierName();

        return $this->{$name};
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return parent::getPassword();
    }

    /**
     * @param string $password
     * @return Base\User
     */
    public function setPassword($password)
    {
        return parent::setPassword($password);
    }

    /**
     * @inheritdoc
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->getPassword();
    }

    /**
     * Get the token value for the "remember me" session.
     * @return string
     */
    public function getRememberToken()
    {
        return parent::getRememberToken();
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param string $value
     * @return Base\User
     */
    public function setRememberToken($value)
    {
        return parent::setRememberToken($value);
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