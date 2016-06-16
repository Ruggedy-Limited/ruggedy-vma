<?php

namespace App\Contracts;


/**
 * An Interface for Entities that have a $id member and the related getId() getter method
 */
interface HasGetId
{
    /**
     * Get's the ID of the object
     *
     * @return int
     */
    function getId();
}