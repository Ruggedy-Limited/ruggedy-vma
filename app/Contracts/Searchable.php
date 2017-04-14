<?php

namespace App\Contracts;

use Doctrine\Common\Collections\Collection;

interface Searchable
{
    /**
     * Search for matching entities
     *
     * @param string $searchTerm
     * @return Collection
     */
    public function search(string $searchTerm): Collection;

    /**
     * Get a Collection of searchable fields for entities in this repository
     *
     * @return \Illuminate\Support\Collection
     */
    public function getSearchableFields(): \Illuminate\Support\Collection;
}