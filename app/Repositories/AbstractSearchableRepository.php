<?php

namespace App\Repositories;

use App\Entities\Asset;
use App\Entities\User;
use App\Entities\Vulnerability;
use App\Entities\Workspace;
use App\Entities\WorkspaceApp;
use App\Transformers\AssetTransformer;
use App\Transformers\UserTransformer;
use App\Transformers\VulnerabilityTransformer;
use App\Transformers\WorkspaceAppTransformer;
use App\Transformers\WorkspaceTransformer;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Illuminate\Support\Collection;

abstract class AbstractSearchableRepository extends EntityRepository
{
    /** Search type constants */
    const SEARCH_TYPE_WORKSPACE     = 1;
    const SEARCH_TYPE_WORKSPACE_APP = 2;
    const SEARCH_TYPE_ASSET         = 3;
    const SEARCH_TYPE_VULNERABILITY = 4;
    const SEARCH_TYPE_USER          = 5;

    /** @var array */
    protected static $searchTypeEntityMap = [
        self::SEARCH_TYPE_WORKSPACE     => Workspace::class,
        self::SEARCH_TYPE_WORKSPACE_APP => WorkspaceApp::class,
        self::SEARCH_TYPE_ASSET         => Asset::class,
        self::SEARCH_TYPE_VULNERABILITY => Vulnerability::class,
        self::SEARCH_TYPE_USER          => User::class,
    ];

    /** @var array */
    protected static $searchTypeTransformerMap = [
        self::SEARCH_TYPE_WORKSPACE     => WorkspaceTransformer::class,
        self::SEARCH_TYPE_WORKSPACE_APP => WorkspaceAppTransformer::class,
        self::SEARCH_TYPE_ASSET         => AssetTransformer::class,
        self::SEARCH_TYPE_VULNERABILITY => VulnerabilityTransformer::class,
        self::SEARCH_TYPE_USER          => UserTransformer::class,
    ];

    /**
     * @param int $searchType
     * @return bool|string
     */
    public static function getTransformerForSearchType(int $searchType)
    {
        if (!self::isValidSearchType($searchType)) {
            return false;
        }

        return static::$searchTypeTransformerMap[$searchType];
    }

    /**
     * Check if a given search type is valid
     *
     * @param $searchType
     * @return bool
     */
    public static function isValidSearchType($searchType): bool
    {
        if (!static::getValidSearchTypes()->get($searchType, false)) {
            return false;
        }

        return true;
    }

    /**
     * Get a Collection of valid search types
     *
     * @return Collection
     */
    public static function getValidSearchTypes(): Collection
    {
        return collect(static::$searchTypeEntityMap);
    }

    /**
     * Search for matching entities
     *
     * @param string $searchTerm
     * @return \Doctrine\Common\Collections\Collection
     */
    public function search(string $searchTerm): DoctrineCollection
    {
        return $this->matching($this->getSearchCriteria($searchTerm));
    }

    /**
     * Get a Criteria object with the relevant search criteria
     *
     * @param string $searchTerm
     * @return Criteria
     */
    protected function getSearchCriteria(string $searchTerm): Criteria
    {
        $criteria = Criteria::create();
        return $this->getSearchableFields()->reduce(function ($criteria, $field) use ($searchTerm) {
            /** @var Criteria $criteria */
            return $criteria->orWhere(Criteria::expr()->contains($field, $searchTerm));
        }, $criteria);
    }

    /**
     * Get Collection of searchable fields for the Entity related to this Repository
     *
     * @return Collection
     */
    abstract protected function getSearchableFields(): Collection;
}