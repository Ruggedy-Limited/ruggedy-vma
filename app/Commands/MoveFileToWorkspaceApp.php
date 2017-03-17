<?php

namespace App\Commands;

class MoveFileToWorkspaceApp extends Command
{
    /** @var int */
    protected $workspaceAppId;

    /** @var int */
    protected $fileId;

    /**
     * MoveFileToWorkspaceApp constructor.
     *
     * @param int $workspaceAppId
     * @param int $fileId
     */
    public function __construct(int $workspaceAppId, int $fileId)
    {
        $this->workspaceAppId = $workspaceAppId;
        $this->fileId         = $fileId;
    }

    /**
     * @return int
     */
    public function getWorkspaceAppId(): int
    {
        return $this->workspaceAppId;
    }

    /**
     * @return int
     */
    public function getFileId(): int
    {
        return $this->fileId;
    }
}