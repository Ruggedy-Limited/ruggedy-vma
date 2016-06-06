<?php

namespace App\Commands;


class CreateProject extends Command
{
    /** @var int */
    protected $userId;
    
    /** @var array */
    protected $projectDetails;

    /**
     * CreateProject constructor.
     *
     * @param int $userId
     * @param array $projectDetails
     */
    public function __construct(int $userId, array $projectDetails)
    {
        $this->userId         = $userId;
        $this->projectDetails = $projectDetails;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return array
     */
    public function getProjectDetails()
    {
        return $this->projectDetails;
    }
}