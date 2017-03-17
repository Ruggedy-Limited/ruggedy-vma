<?php

namespace App\Commands;

class Search extends Command
{
    /** @var string */
    protected $searchTerm;

    /** @var int */
    protected $searchType;

    /**
     * Search constructor.
     *
     * @param string $searchTerm
     * @param int $searchType
     */
    public function __construct(string $searchTerm, int $searchType)
    {
        $this->searchTerm = $searchTerm;
        $this->searchType = $searchType;
    }

    /**
     * @return string
     */
    public function getSearchTerm(): string
    {
        return $this->searchTerm;
    }

    /**
     * @return int
     */
    public function getSearchType(): int
    {
        return $this->searchType;
    }
}