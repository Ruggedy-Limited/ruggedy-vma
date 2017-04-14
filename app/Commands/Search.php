<?php

namespace App\Commands;

class Search extends Command
{
    /** @var string */
    protected $searchTerm;

    /**
     * Search constructor.
     *
     * @param string $searchTerm
     */
    public function __construct(string $searchTerm)
    {
        $this->searchTerm = $searchTerm;
    }

    /**
     * @return string
     */
    public function getSearchTerm(): string
    {
        return $this->searchTerm;
    }
}