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
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return array
     */
    public function getRequestedChanges()
    {
        return $this->requestedChanges;
    }

    /**
     * @param array $requestedChanges
     */
    public function setRequestedChanges($requestedChanges)
    {
        $this->requestedChanges = $requestedChanges;
    }
}