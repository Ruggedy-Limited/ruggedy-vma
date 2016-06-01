<?php

namespace App\Commands;


class GetListOfUsersInTeam extends Command
{
    /** @var  integer */
    protected $teamId;

    /**
     * InviteToTeamCommand constructor.
     *
     * @param int $teamId
     */
    public function __construct($teamId)
    {
        $this->teamId = $teamId;
    }

    /**
     * @return int
     */
    public function getTeamId()
    {
        return $this->teamId;
    }

    /**
     * @param int $teamId
     */
    public function setTeamId($teamId)
    {
        $this->teamId = $teamId;
    }
}