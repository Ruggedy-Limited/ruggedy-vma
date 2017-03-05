<?php

namespace App\Commands;


use App\Entities\Base\AbstractEntity;

abstract class CreateSomething extends Command
{
    /** @var int */
    protected $id;

    /** @var AbstractEntity */
    protected $entity;

    /** @var bool */
    protected $multiMode;

    /**
     * CreateSomething constructor.
     *
     * @param int $id
     * @param AbstractEntity $entity
     * @param bool $multiMode
     */
    public function __construct(int $id, AbstractEntity $entity, bool $multiMode = false)
    {
        $this->id        = $id;
        $this->entity    = $entity;
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
     * @return AbstractEntity
     */
    public function getEntity(): AbstractEntity
    {
        return $this->entity;
    }

    /**
     * @return boolean
     */
    public function isMultiMode(): bool
    {
        return $this->multiMode;
    }
}