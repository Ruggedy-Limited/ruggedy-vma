<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;
use stdClass;


/**
 * @ORM\Entity(repositoryClass="App\Repositories\ComponentRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Component extends Base\Component
{
    /** The database column name for the field that hiolds the component names */
    const COMPONENT_NAME_FIELD = 'name';

    /**
     * Override the AbstractEntity method, just to provide a default set of attributes to include when coercing to
     * stdClass for JSON
     *
     * @param array $onlyTheseAttributes
     * @return stdClass
     */
    public function toStdClass($onlyTheseAttributes = [])
    {
        return $this->getName();
    }
}