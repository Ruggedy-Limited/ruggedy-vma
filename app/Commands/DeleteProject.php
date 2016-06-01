<?php

namespace App\Commands;


class DeleteProject extends Command
{
    /** @var int */
    protected $projectId;
    
    /** @var bool */
    protected $confirm;

    /**
     * DeleteProject constructor.
     *
     * @param int $projectId
     * @param bool $confirm
     */
    public function __construct(int $projectId, bool $confirm)
    {
        $this->projectId = $projectId;
        $this->confirm   = $confirm;
    }

    /**
     * @return int
     */
    public function getProjectId()
    {
        return $this->projectId;
    }

    /**
     * @param int $projectId
     */
    public function setProjectId($projectId)
    {
        $this->projectId = $projectId;
    }

    /**
     * @return boolean
     */
    public function isConfirm()
    {
        return $this->confirm;
    }

    /**
     * @param boolean $confirm
     */
    public function setConfirm($confirm)
    {
        $this->confirm = $confirm;
    }
}