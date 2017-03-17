<?php

namespace App\Commands;


abstract class GetListOfSomething extends Command
{

    /** @var int */
    protected $id;
    
    /**
     * GetListOfSomething constructor.
     *
     * @param int $id
     */
    public function __construct(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}