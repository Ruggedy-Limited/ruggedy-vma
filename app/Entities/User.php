<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;
use Illuminate\Contracts\Auth\Authenticatable;
use stdClass;

/**
 * App\Entities\User
 *
 * @ORM\Entity(repositoryClass="App\Repositories\UserRepository")
 * @ORM\HasLifecycleCallbacks
 */
class User extends Base\User implements Authenticatable
{
    /**
     * Check if a User owns a team
     *
     * @param Team $team
     * @return bool
     */
    public function ownsTeam(Team $team)
    {
        return $this->getTeams()->contains($team);
    }

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
        return $this->getId();
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
     * @inheritdoc
     * @return string
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
    }
}