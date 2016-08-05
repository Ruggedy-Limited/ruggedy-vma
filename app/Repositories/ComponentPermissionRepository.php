<?php

namespace App\Repositories;

use App\Entities\ComponentPermission;
use Doctrine\ORM\EntityRepository;
use Illuminate\Support\Collection;


class ComponentPermissionRepository extends EntityRepository
{
    /**
     * Get all the permissions for a specific component instance
     *
     * @param int $componentInstanceId
     * @return Collection
     */
    public function findByComponentAndComponentInstanceId(int $componentId, $componentInstanceId): Collection
    {
        if (!isset($componentId, $componentInstanceId)) {
            return new Collection();
        }

        $resultSet = $this->findBy([
            ComponentPermission::COMPONENT_ID => $componentId,
            ComponentPermission::INSTANCE_ID  => $componentInstanceId,
        ]);

        if (empty($resultSet)) {
            return new Collection();
        }

        return new Collection($resultSet);
    }

    /**
     * Find a permission a permission entry by component_id, instance_id and user_id
     *
     * @param int $componentId
     * @param int $componentInstanceId
     * @param int $userId
     * @return null|object
     */
    public function findOneByComponentInstanceAndUserIds(int $componentId, int $componentInstanceId, int $userId)
    {
        if (!isset($componentId, $componentInstanceId, $userId)) {
            return null;
        }

        return $this->findOneBy([
            ComponentPermission::COMPONENT_ID => $componentId,
            ComponentPermission::INSTANCE_ID  => $componentInstanceId,
            ComponentPermission::USER_ID      => $userId,
        ]);
    }
}