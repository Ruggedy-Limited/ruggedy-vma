<?php

namespace App\Commands;


abstract class GetListOfSomething extends Command
{

    /** @var int */
    protected $id;
    
    /**
     * GetListOfUsersProjects constructor.
     *
     * @param int $userId
     */
    public function __construct(int $userId)
    {
        $this->id = $userId;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}