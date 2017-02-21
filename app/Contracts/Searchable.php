<?php

namespace App\Contracts;

use Doctrine\Common\Collections\Collection;

interface Searchable
{
    /**
     * Get a Criteria object to use in constructing the search query
     *
     * @param string $searchTerm
     * @return Collection
     */
    public function search(string $searchTerm): Collection;
}