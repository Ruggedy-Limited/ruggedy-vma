<?php

namespace App\Repositories;

use App\Entities\User;
use Doctrine\ORM\EntityRepository;


class UserRepository extends EntityRepository
{
    /**
     * Find a user by their ID and remember me token
     *
     * @param $identifier
     * @param $token
     * @return null|User
     */
    public function findByIdAndRememberMeToken($identifier, $token)
    {
        if (!isset($identifier, $token)) {
            return null;
        }

        return $this->findOneBy([
            'id'             => $identifier,
            'remember_token' => $token,
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
}