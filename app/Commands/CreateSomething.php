<?php

namespace App\Commands;


abstract class CreateSomething extends Command
{
    /** @var int */
    protected $id;

    /** @var array */
    protected $details;

    /** @var bool */
    protected $multiMode;

    /**
     * CreateSomething constructor.
     *
     * @param int $id
     * @param array $details
     * @param bool $multiMode
     */
    public function __construct(int $id, array $details, bool $multiMode = false)
    {
        $this->id        = $id;
        $this->details   = $details;
        $this->multiMode = $multiMode;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getDetails(): array
    {
        return $this->details;
    }

    /**
     * @return boolean
     */
    public function isMultiMode(): bool
    {
        return $this->multiMode;
    }
}