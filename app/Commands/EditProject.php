<?php

namespace App\Commands;


class EditProject extends Command
{
    /** @var int */
    protected $projectId;

    /** @var array */
    protected $requestedChanges;

    /**
     * EditProject constructor.
     * 
     * @param int $projectId
     * @param array $requestedChanges
     */
    public function __construct(int $projectId, array $requestedChanges)
    {
        $this->projectId        = $projectId;
        $this->requestedChanges = $requestedChanges;
    }

    /**
     * @return int
     */
    public function getProjectId()
    {
        return $this->projectId;
    }

    /**
     * @return array
     */
    public function getRequestedChanges()
    {
        return $this->requestedChanges;
    }
}