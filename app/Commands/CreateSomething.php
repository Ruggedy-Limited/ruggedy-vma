<?php

namespace App\Commands;


abstract class CreateSomething extends Command
{
    /** @var int */
    protected $id;

    /** @var string */
    protected $name;

    /** @var array */
    protected $details;

    /**
     * CreateSomething constructor.
     *
     * @param int $id
     * @param array $details
     */
    public function __construct(int $id, string $name, array $details)
    {
        $this->id      = $id;
        $this->name    = $name;
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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getDetails()
    {
        return $this->details;
    }
}