<?php

namespace App\Repositories;

use App\Contracts\Searchable;
use App\Entities\Asset;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;
use Illuminate\Support\Collection;
use LaravelDoctrine\ORM\Pagination\PaginatesFromRequest;

class AssetRepository extends AbstractSearchableRepository implements Searchable
{
    use PaginatesFromRequest;

    /**
     * Attempt to find an existing Asset by the given criteria, but if not found create a new Asset populated with
     * the given $criteria array
     *
     * @param array $data
     * @return Asset|null
     */
    public function findOrCreateOneBy(array $data)
    {
        // We must have some asset details and must definitely have a Workspace ID with which to associate the Asset
        if (empty($data) || empty($data[Asset::FILE_ID])) {
            return null;
        }

        // If we have a hostname value, either find an existing Asset with that hostname or create a new one
        if (!empty($data[Asset::HOSTNAME])) {

            $asset = $this->findOneBy(array_intersect_key($data, [
                Asset::HOSTNAME      => null,
                Asset::FILE_ID       => null,
            ]));

            if (empty($asset)) {
                return $this->createNewAssetEntity($data);
            }

            return $asset->setFromArray($data);
        }

        $asset = $this->findOneBy(array_intersect_key($data, [
            Asset::IP_ADDRESS_V4 => null,
            Asset::FILE_ID       => null,
        ]));

        if (empty($asset)) {
            return $this->createNewAssetEntity($data);
        }

        // Update the Asset with new data and return it
        return $asset->setFromArray($data);
    }

    /**
     * Prepare a new Asset Entity
     *
     * @param array $dataToPopulate
     * @return Asset
     */
    protected function createNewAssetEntity(array $dataToPopulate = []): Asset
    {
        $asset = new Asset();

        if (!empty($dataToPopulate)) {
            $asset->setFromArray($dataToPopulate);
        }

        // For new Assets we always set suppressed and deleted to false
        $asset->setSuppressed(false);
        $asset->setDeleted(false);

        return $asset;
    }

    /**
     * Get the Doctrine QueryBuilder expression to search for an existing asset based on the possible identifiers
     * provided as criteria. If we are not able to build a good set of criteria we return a QueryBuilder instance with
     * no WHERE part set, and this must be checked to determine whether or not to execute the query
     *
     * @param Collection $criteria
     * @return QueryBuilder
     */
    protected function whereAssetWithCriteriaExists(Collection $criteria, int $workspaceId): QueryBuilder
    {
        // Create a QueryBuilder instance and make sure the criteria are not empty.
        $queryBuilder = $this->createQueryBuilder('a');
        if ($criteria->isEmpty()) {
            return $queryBuilder;
        }

        // Iterate over the given criteria and add an OR statement to the WHERE clause if applicable
        $arguments = $criteria->map(function ($value, $column) use ($queryBuilder) {
            // exit early on empty values, no point querying for an empty
            if (empty($value)) {
                return null;
            }

            // Add an ORDER BY clause to the query. We order by each criteria in descending order so that records with
            // IP address values appear first, then records with hostname values and then records with netbios entries
            $queryBuilder->orderBy($queryBuilder->expr()->desc('a.' . $column));

            return $queryBuilder->expr()->eq('a.' . $column, "'$value'");
        })->filter(function ($value, $column) {
            return isset($value);
        })->toArray();

        // There are no valid criteria, return a queryBuilder instance without a WHERE part
        if (empty($arguments)) {
            return $queryBuilder;
        }

        $arguments = array_values($arguments);

        $queryBuilder->andWhere(
            // Filter Assets by Workspace first
            $queryBuilder->expr()->eq('a.' . Asset::WORKSPACE_ID, $workspaceId),
            // Only Assets that don't have a hostname
            $queryBuilder->expr()->isNull('a.' . Asset::HOSTNAME),
            // Additional Filters as an OR
            $queryBuilder->expr()->orX(...$arguments)
        );

        // Only the first Asset result
        $queryBuilder->setMaxResults(1);

        return $queryBuilder;
    }

    /**
     * @param int $fileId
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function findByFileQuery(int $fileId = 0)
    {
        if (!isset($fileId)) {
            $fileId = 0;
        }

        return $this->paginate(
            $this->createQueryBuilder($this->getQueryBuilderAlias())
                ->addSelect("SUM(v.severity) AS risk")
                ->leftJoin('a.vulnerabilities', 'v')
                ->addCriteria(
                    Criteria::create()->where(
                        Criteria::expr()->eq(Asset::FILE_ID, $fileId)
                    )
                )
                ->groupBy('a.id')
                ->orderBy('a.name', Criteria::ASC)
                ->orderBy('risk', Criteria::DESC)
                ->getQuery(),
            $this->getPerPage(),
            $this->getPageName(),
            false
        )->setPageName($this->getPageName());
    }

    /**
     * Get all the sorted, paginated Assets related to a Vulnerability
     *
     * @param int $vulnerabilityId
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function findByVulnerabilityQuery(int $vulnerabilityId = 0)
    {
        if (!isset($vulnerabilityId)) {
            $vulnerabilityId = 0;
        }

        return $this->paginate(
            $this->createQueryBuilder($this->getQueryBuilderAlias())
                 ->addSelect("SUM(v.severity) AS risk")
                 ->leftJoin('a.vulnerabilities', 'v')
                 ->leftJoin('a.vulnerabilities', 'v2')
                 ->addCriteria(
                     Criteria::create()->where(
                         Criteria::expr()->eq('v2.id', $vulnerabilityId)
                     )
                 )
                 ->groupBy('a.id')
                 ->orderBy('a.name', Criteria::ASC)
                 ->orderBy('risk', Criteria::DESC)
                 ->getQuery(),
            $this->getPerPage(),
            $this->getPageName(),
            false
        )->setPageName($this->getPageName());
    }

    /**
     * @inheritdoc
     *
     * @return Collection
     */
    public function getSearchableFields(): Collection
    {
        return collect([Asset::NAME, Asset::HOSTNAME, Asset::IP_ADDRESS_V4]);
    }

    /**
     * @inheritdoc
     *
     * @return string
     */
    protected function getQueryBuilderAlias(): string
    {
        return 'a';
    }

    /**
     * @inheritdoc
     *
     * @return string
     */
    protected function getPageName(): string
    {
        return 'assets_page';
    }

    protected function getPerPage(): int
    {
        return 9;
    }

    /**
     * @inheritdoc
     *
     * @param QueryBuilder $queryBuilder
     * @return QueryBuilder
     */
    protected function addOrdering(QueryBuilder $queryBuilder): QueryBuilder
    {
        return $queryBuilder->orderBy('a.name', Criteria::ASC);
    }
}