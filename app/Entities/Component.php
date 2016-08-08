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
    /**
     * Override the AbstractEntity method, just to provide a default set of attributes to include when coercing to
     * stdClass for JSON
     *
     * @param array $onlyTheseAttributes
     * @return stdClass
     */
    public function toStdClass($onlyTheseAttributes = [])
    {
        // Set a list of attributes to include by default when no specific list is given
        if (empty($onlyTheseAttributes)) {
            $onlyTheseAttributes = ['name'];
        }

        return parent::toStdClass($onlyTheseAttributes);
    }
}