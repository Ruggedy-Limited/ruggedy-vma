<?php

namespace App\Commands;


class GetListOfUsersProjects extends Command
{
    /** @var int */
    protected $userId;

    /**
     * GetListOfUsersProjects constructor.
     *
     * @param int $userId
     */
    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }
}