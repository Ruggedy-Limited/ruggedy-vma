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
    /** Database column names */
    const COMPONENT_NAME_FIELD = 'name';
    const COMPONENT_CLASS_NAME = 'class_name';

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