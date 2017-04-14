<?php

namespace App\Repositories;

use App\Contracts\Searchable;
use App\Entities\User;
use Illuminate\Support\Collection;


class UserRepository extends AbstractSearchableRepository implements Searchable
{
    /**
     * Find a user by their ID and remember me token
     *
     * @param $identifier
     * @param $token
     * @return null|User|object
     */
    public function findByIdAndRememberMeToken($identifier, $token)
    {
        if (!isset($identifier, $token)) {
            return null;
        }

        return $this->findOneBy([
            User::ID             => $identifier,
            User::REMEMBER_TOKEN => $token,
        ]);
    }

    /**
     * Find a User by the given credentials
     *
     * @param array $credentials
     * @return null|object
     */
    public function findByCredentials(array $credentials)
    {
        if (empty($credentials)) {
            return null;
        }

        return $this->findOneBy($credentials);
    }

    /**
     * @inheritdoc
     *
     * @return Collection
     */
    public function getSearchableFields(): Collection
    {
        return collect([User::NAME, User::EMAIL]);
    }
}