<?php

namespace App\Contracts;

use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface Searchable
{
    /**
     * Search for matching entities
     *
     * @param string $searchTerm
     * @return LengthAwarePaginator
     */
    public function search(string $searchTerm): LengthAwarePaginator;

    /**
     * Get a Collection of searchable fields for entities in this repository
     *
     * @return \Illuminate\Support\Collection
     */
    public function getSearchableFields(): Collection;
}