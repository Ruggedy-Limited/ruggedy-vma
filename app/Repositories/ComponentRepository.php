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
     * @return array|null
     */
    public function findByComponentName(string $componentName)
    {
        if (empty($componentName)) {
            return null;
        }
        
        return $this->findBy([Component::COMPONENT_NAME_FIELD => $componentName]);
    }
}