<?php

namespace App\Commands;


class EditWorkspace extends Command
{
    /** @var int */
    protected $workspaceId;

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
        $this->workspaceId      = $projectId;
        $this->requestedChanges = $requestedChanges;
    }

    /**
     * @return int
     */
    public function getWorkspaceId()
    {
        return $this->workspaceId;
    }

    /**
     * @return array
     */
    public function getRequestedChanges()
    {
        return $this->requestedChanges;
    }
}