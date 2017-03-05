<?php

namespace App\Auth;

use App\Entities\User;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Hashing\Hasher;
use LaravelDoctrine\ORM\Facades\EntityManager;


class DoctrineUserProvider implements UserProvider
{
    /** @var  User */
    protected $model;
    /** @var  Hasher */
    protected $hasher;
    /** @var  UserRepository */
    protected $repository;

    /**
     * DoctrineUserProvider constructor.
     * @param Authenticatable $model
     * @param Hasher $hasher
     * @param UserRepository $repository
     */
    public function __construct(Authenticatable $model, Hasher $hasher, UserRepository $repository)
    {
        $this->model      = $model;
        $this->hasher     = $hasher;
        $this->repository = $repository;
    }

    /**
     * Get a user by their ID
     * 
     * @param mixed $identifier
     * @return null|object
     */
    public function retrieveById($identifier)
    {
        return $this->repository->find($identifier);
    }

    /**
     * Get a user by their ID and remember me token
     * 
     * @param mixed $identifier
     * @param string $token
     * @return User|null
     */
    public function retrieveByToken($identifier, $token)
    {
        return $this->repository->findByIdAndRememberMeToken($token, $token);
    }

    /**
     * Update the User's remember me token
     * 
     * @param Authenticatable $user
     * @param string $token
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        $user->setRememberToken($token);
        EntityManager::persist($user);
        EntityManager::flush();
    }

    /**
     * Retrieve a user based on given credentials
     *
     * @param array $credentials
     * @return Authenticatable|null|User
     */
    public function retrieveByCredentials(array $credentials)
    {
        return $this->repository->findByCredentials($credentials);
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param Authenticatable $user
     * @param array $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        return $this->hasher->check($credentials['password'], $user->getAuthPassword());
    }
}