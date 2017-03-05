<?php

namespace App\Contracts;

use Illuminate\Support\Collection;

interface GeneratesUniqueHash
{
    /**
     * Get a hash value of all the property values for all the properties that are set
     *
     * @return string
     */
    public function getHash(): string;

    /**
     * Get a Collection of property names to use in generating a unique hash
     *
     * @return Collection
     */
    public function getUniqueKeyColumns(): Collection;
}