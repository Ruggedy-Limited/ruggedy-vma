<?php

namespace App\Commands;


class CreateWorkspace extends Command
{
    /** @var int */
    protected $projectId;
    
    /** @var array */
    protected $workspaceDetails;

    /**
     * CreateWorkspace constructor.
     * 
     * @param int $projectId
     * @param array $workspaceDetails
     */
    public function __construct(int $projectId, array $workspaceDetails)
    {
        $this->projectId        = $projectId;
        $this->workspaceDetails = $workspaceDetails;
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
    public function getWorkspaceDetails()
    {
        return $this->workspaceDetails;
    }
}