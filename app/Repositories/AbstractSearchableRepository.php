<?php

namespace App\Repositories;

use App\Entities\Asset;
use App\Entities\User;
use App\Entities\Vulnerability;
use App\Entities\Workspace;
use App\Entities\WorkspaceApp;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

abstract class AbstractSearchableRepository extends EntityRepository
{
    /** Search type constants */
    const SEARCH_TYPE_WORKSPACE     = 1;
    const SEARCH_TYPE_WORKSPACE_APP = 2;
    const SEARCH_TYPE_ASSET         = 3;
    const SEARCH_TYPE_VULNERABILITY = 4;
    const SEARCH_TYPE_USER          = 5;

    /** Default per page */
    const DEFAULT_PER_PAGE = 10;

    /** @var array */
    protected static $searchTypeEntityMap = [
        self::SEARCH_TYPE_WORKSPACE     => Workspace::class,
        self::SEARCH_TYPE_WORKSPACE_APP => WorkspaceApp::class,
        self::SEARCH_TYPE_ASSET         => Asset::class,
        self::SEARCH_TYPE_VULNERABILITY => Vulnerability::class,
        self::SEARCH_TYPE_USER          => User::class,
    ];

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
     * @return LengthAwarePaginator
     */
    public function search(string $searchTerm): LengthAwarePaginator
    {
        return $this->paginate(
            $this->addOrdering(
                $this->createQueryBuilder($this->getQueryBuilderAlias())
                     ->addCriteria(
                         $this->getSearchCriteria($searchTerm)
                     )
            )->getQuery(),
            $this->getPerPage(),
            $this->getPageName(),
            false
        );
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
    abstract public function getSearchableFields(): Collection;

    /**
     * Get the default QueryBuilder alias for a repository
     *
     * @return string
     */
    abstract protected function getQueryBuilderAlias(): string;

    /**
     * Get the page name to be used by the paginator. This is used in the query string
     * and then used by the paginator to determine the current page.
     *
     * @return string
     */
    abstract protected function getPageName(): string;

    /**
     * Get the number of results to display on a page
     *
     * @return int
     */
    protected function getPerPage(): int
    {
        return self::DEFAULT_PER_PAGE;
    }

    /**
     * @param Query  $query
     * @param int    $perPage
     * @param bool   $fetchJoinCollection
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    abstract public function paginate(Query $query, $perPage, $pageName = 'page', $fetchJoinCollection = true);

    /**
     * Template for adding an ORDER BY clause, to be extended by adding search ordering in extended classes.
     * The default will add no ordering to the query, but will just return th query as is.
     *
     * @param QueryBuilder $queryBuilder
     * @return QueryBuilder
     */
    protected function addOrdering(QueryBuilder $queryBuilder): QueryBuilder
    {
        return $queryBuilder;
    }
}