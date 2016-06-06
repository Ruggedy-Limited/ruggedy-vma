<?php

namespace App\Commands;


class EditUserAccount extends Command
{
    /** @var int */
    protected $userId;
    /** @var array */
    protected $requestedChanges;

    /**
     * EditUserAccount constructor.
     *
     * @param $userId
     * @param $requestedChanges
     */
    public function __construct($userId, array $requestedChanges)
    {
        $this->userId           = $userId;
        $this->requestedChanges = $requestedChanges;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return array
     */
    public function getRequestedChanges()
    {
        return $this->requestedChanges;
    }
}