<?php

namespace App\Commands;


class DeleteWorkspace extends Command
{
    /** @var int */
    protected $workspaceId;

    /** @var bool */
    protected $confirm;

    /**
     * DeleteWorkspace constructor.
     *
     * @param int $workspaceId
     * @param bool $confirm
     */
    public function __construct(int $workspaceId, $confirm = false)
    {
        $this->workspaceId = $workspaceId;
        $this->confirm     = $confirm;
    }

    /**
     * @return int
     */
    public function getWorkspaceId()
    {
        return $this->workspaceId;
    }

    /**
     * @return boolean
     */
    public function isConfirm()
    {
        return $this->confirm;
    }
}