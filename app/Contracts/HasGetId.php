<?php

namespace App\Contracts;


interface HasGetId
{
    /**
     * Get's the ID of the object
     *
     * @return int
     */
    function getId();
}