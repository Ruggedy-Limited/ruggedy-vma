<?php

namespace App\Commands;


abstract class EditSomething extends Command
{
    /** @var int */
    protected $id;

    /** @var array */
    protected $requestedChanges;

    /**
     * EditSomething constructor.
     *
     * @param int $id
     * @param array $requestedChanges
     */
    public function __construct(int $id, array $requestedChanges)
    {
        $this->id               = $id;
        $this->requestedChanges = $requestedChanges;
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
    public function getRequestedChanges()
    {
        return $this->requestedChanges;
    }
}