<?php

namespace App\Repositories;

use App\Entities\Component;
use Doctrine\ORM\EntityRepository;


class ComponentRepository extends EntityRepository
{
    /**
     * Get the record by the Component name
     *
     * @param string $componentName
     * @return object|null
     */
    public function findOneByComponentName(string $componentName)
    {
        if (empty($componentName)) {
            return null;
        }
        
        return $this->findOneBy([Component::NAME => $componentName]);
    }
}