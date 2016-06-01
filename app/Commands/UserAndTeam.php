<?php

namespace App\Commands;


abstract class UserAndTeam extends Command
{
    /** @var  int */
    protected $teamId;
    /** @var  int */
    protected $userId;

    /**
     * RemoveFromTeam constructor.
     *
     * @param int $teamId
     * @param int $userId
     */
    public function __construct($teamId = null, $userId = null)
    {
        $this->teamId = $teamId;
        $this->userId = $userId;
    }

    /**
     * @return mixed
     */
    public function getTeamId()
    {
        return $this->teamId;
    }

    /**
     * @param mixed $teamId
     */
    public function setTeamId($teamId)
    {
        $this->teamId = $teamId;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }
}