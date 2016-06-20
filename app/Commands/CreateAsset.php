<?php

namespace App\Commands;


class CreateAsset extends CreateSomething
{
    /** @var integer */
    protected $workspaceId;

    /** @var integer */
    protected $userId;

    /**
     * CreateAsset constructor.
     *
     * @param int $id
     * @param string $name
     * @param array $details
     * @param int $workspaceId
     * @param int $userId
     */
    public function __construct(int $id, string $name, array $details, int $workspaceId, int $userId)
    {
        parent::__construct($id, $name, $details);

        $this->workspaceId = $workspaceId;
        $this->userId      = $userId;
    }

    /**
     * @return int
     */
    public function getWorkspaceId()
    {
        return $this->workspaceId;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }
}