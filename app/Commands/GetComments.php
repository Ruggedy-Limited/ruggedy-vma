<?php

namespace App\Commands;

class GetComments extends GetListOfSomething
{
    /** @var int */
    protected $vulnerabilityId;

    /**
     * GetComments constructor.
     *
     * @param int $id
     * @param int $vulnerabilityId
     */
    public function __construct($id, int $vulnerabilityId)
    {
        $this->vulnerabilityId = $vulnerabilityId;
        parent::__construct($id);
    }

    /**
     * @return int
     */
    public function getVulnerabilityId(): int
    {
        return $this->vulnerabilityId;
    }
}