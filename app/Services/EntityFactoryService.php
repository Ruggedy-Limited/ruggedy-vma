<?php

namespace App\Services;

use App\Entities\Base\AbstractEntity;

class EntityFactoryService
{
    /**
     * Create a new entity instance and populate it if a details array is provided
     *
     * @param string $entityClass
     * @param array $details
     * @return AbstractEntity|null
     */
    public static function makeEntity(string $entityClass, array $details = [])
    {
        if (!class_exists($entityClass)) {
            return null;
        }

        if (empty($details)) {
            return new $entityClass();
        }

        /** @var AbstractEntity $entity */
        $entity = new $entityClass();
        return $entity->setFromArray($details);
    }
}