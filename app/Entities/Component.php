<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="App\Repositories\ComponentRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Component extends Base\Component
{
    /** The database column name for the field that hiolds the component names */
    const COMPONENT_NAME_FIELD = 'name';
}