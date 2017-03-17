<?php

namespace App\Commands;

abstract class GetSomething extends Command
{
    /** @var int */
    protected $id;

    /**
     * GetSomething constructor.
     * @param int $id
     */
    public function __construct(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
}