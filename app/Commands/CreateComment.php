<?php

namespace App\Commands;

use App\Entities\Base\AbstractEntity;

class CreateComment extends CreateSomething
{
    /** @var int */
    protected $vulnerabilityId;

    /**
     * CreateComment constructor.
     *
     * @param int $id
     * @param AbstractEntity $entity
     * @param int $vulnerabilityId
     * @param bool $multiMode
     */
    public function __construct($id, AbstractEntity $entity, int $vulnerabilityId, $multiMode = false)
    {
        $this->vulnerabilityId = $vulnerabilityId;
        parent::__construct($id, $entity, $multiMode);
    }

    /**
     * @return int
     */
    public function getVulnerabilityId(): int
    {
        return $this->vulnerabilityId;
    }
}