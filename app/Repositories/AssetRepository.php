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
     * @param array $data
     * @return Asset|null
     */
    public function findOrCreateOneBy(array $data)
    {
        if (empty($data)) {
            return null;
        }

        // Only search by these possible identifiers
        $criteria = array_intersect_key($data, [
            'hostname'      => true,
            'ip_address_v4' => true,
            'ip_address_v6' => true
        ]);

        // Attempt to retrieve the Asset from the DB or create a new Asset entity if no matching Asset is found
        $asset = $this->findOneBy($criteria);
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
}