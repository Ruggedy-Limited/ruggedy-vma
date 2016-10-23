<?php

namespace App\Repositories;

use App\Entities\Asset;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Illuminate\Support\Collection;

class AssetRepository extends EntityRepository
{
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
        if (empty($data) || empty($data[Asset::WORKSPACE_ID])) {
            return null;
        }

        $workspaceId = $data[Asset::WORKSPACE_ID];

        // If we have a hostname value, either find an existing Asset with that hostname or create a new one
        if (!empty($data[Asset::HOSTNAME])) {
            $hostname = $data[Asset::HOSTNAME];

            $asset = $this->findOneBy([
                Asset::HOSTNAME     => $hostname,
                Asset::WORKSPACE_ID => $workspaceId
            ]);

            if (empty($asset)) {
                return $this->createNewAssetEntity($data);
            }

            return $asset->setFromArray($data);
        }

        // Only search by these possible identifiers
        $criteria = new Collection(
            array_intersect_key($data, [
                Asset::IP_ADDRESS_V4 => null,
                Asset::NETBIOS       => null,
            ])
        );

        $queryBuilder = $this->whereAssetWithCriteriaExists($criteria, $workspaceId);
        if (empty($queryBuilder->getDQLPart('where'))) {
            return null;
        }

        $asset = $queryBuilder->getQuery()->getOneOrNullResult();
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
}