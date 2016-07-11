<?php

namespace App\Repositories;

use App\Entities\Asset;
use Doctrine\ORM\EntityRepository;

class AssetRepository extends EntityRepository
{
    /**
     * Attempt to find an existing Asset by the given criteria, but if not found create a new Asset populated with
     * the given $criteria array
     *
     * @param array $criteria
     * @return Asset|null
     */
    public function findOrCreateOneBy(array $criteria)
    {
        if (empty($criteria)) {
            return null;
        }

        $searchCriteria = array_intersect_key($criteria, [
            'hostname'      => true,
            'ip_address_v4' => true,
            'ip_address_v6' => true
        ]);

        $asset = $this->findOneBy($searchCriteria);
        if (empty($asset)) {
            $asset = new Asset();
            $asset->setSuppressed(false);
            $asset->setDeleted(false);
        }

        $asset->setFromArray($criteria);
        return $asset;
    }
}