<?php

namespace App\Commands;


abstract class CreateSomething extends Command
{
    /** @var int */
    protected $id;

    /** @var array */
    protected $details;

    /**
     * CreateSomething constructor.
     *
     * @param int $id
     * @param array $details
     */
    public function __construct(int $id, array $details)
    {
        $this->id             = $id;
        $this->details = $details;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getDetails()
    {
        return $this->details;
    }
}