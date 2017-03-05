<?php

namespace App\Commands;


abstract class DeleteSomething extends Command
{
    /** @var int */
    protected $id;

    /** @var bool */
    protected $confirm;

    /**
     * DeleteSomething constructor.
     *
     * @param int $id
     * @param bool $confirm
     */
    public function __construct(int $id, $confirm = false)
    {
        $this->id      = $id;
        $this->confirm = $confirm;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return boolean
     */
    public function isConfirm()
    {
        return $this->confirm;
    }
}