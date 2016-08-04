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
        if (empty($data)) {
            return null;
        }

        // Only search by these possible identifiers
        $criteria = new Collection(
            array_intersect_key($data, [
                'ip_address_v4' => null,
                'hostname'      => null,
                'netbios'       => null,
            ])
        );


        $queryBuilder = $this->whereAssetWithCriteriaExists($criteria);
        if (empty($queryBuilder->getDQLPart('where'))) {
            return null;
        }

        // Attempt to retrieve the Asset from the DB or create a new Asset entity if no matching Asset is found
        $query = $queryBuilder->getQuery()->getSQL();
        $asset = $queryBuilder->getQuery()->getOneOrNullResult();
        if (empty($asset)) {
            $asset = new Asset();
            // For new Assets we always set suppressed and deleted to false
            $asset->setSuppressed(false);
            $asset->setDeleted(false);
        }

        // Populate the Asset with the given data and return
        $asset->setFromArray($data);
        return $asset;
    }

    /**
     * Get the Doctrine QueryBuilder expression to seach for an existing asset based on the possible identifiers
     * provided as criteria
     *
     * @param Collection $criteria
     * @return QueryBuilder
     */
    protected function whereAssetWithCriteriaExists(Collection $criteria): QueryBuilder
    {
        // Create a QueryBuilder instance and make sure the criteria are not empty
        $queryBuilder = $this->createQueryBuilder('a');
        if ($criteria->isEmpty()) {
            return $queryBuilder;
        }

        // Iterate over the given criteria and add an OR statement to the WHERE clause if applicable
        $criteria->each(function ($value, $column) use ($queryBuilder) {
            if (empty($value)) {
                return true;
            }

            $queryBuilder->orWhere(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->isNotNull('a.' . $column),
                    $queryBuilder->expr()->eq('a.' . $column, "'$value'")
                )
            );

            // Add an ORDER BY clause to the query. We order by each criteria in descending order so that records with
            // IP address values appear first, then records with hostname values and then records with netbios entries
            $queryBuilder->orderBy($queryBuilder->expr()->desc('a.' . $column));

            return true;
        });

        $queryBuilder->setMaxResults(1);

        return $queryBuilder;
    }
}