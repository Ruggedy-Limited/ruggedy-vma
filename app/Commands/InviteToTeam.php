<?php

namespace App\Commands;


class InviteToTeam extends Command
{
    /** @var  integer */
    protected $teamId;
    /** @var  string */
    protected $email;

    /**
     * InviteToTeamCommand constructor.
     * 
     * @param int $teamId
     * @param string $email
     */
    public function __construct($teamId, $email)
    {
        $this->teamId = $teamId;
        $this->email  = $email;
    }

    /**
     * @return int
     */
    public function getTeamId()
    {
        return $this->teamId;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }
}