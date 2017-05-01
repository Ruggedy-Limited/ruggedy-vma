<?php

namespace App\Repositories;

use App\Contracts\Searchable;
use App\Entities\User;
use Doctrine\Common\Collections\Criteria;
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
            User::DELETED        => false,
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

        $credentials[User::DELETED] = false;

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

    /**
     * Get a Criteria object with the relevant search criteria
     *
     * @param string $searchTerm
     * @return Criteria
     */
    protected function getSearchCriteria(string $searchTerm): Criteria
    {
        return parent::getSearchCriteria($searchTerm)->andWhere(Criteria::expr()->eq(User::DELETED, false));
    }

    /**
     * Override the parent method for this repository so that all searches for Users only search for Users that don't
     * have the deleted flag set to true, i.e. Users that have not been deleted.
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @return null|object
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        if (empty($criteria[User::DELETED])) {
            $criteria[User::DELETED] = false;
        }

        return parent::findOneBy($criteria, $orderBy);
    }
}