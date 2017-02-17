<?php

namespace App\Commands;

use App\Entities\Base\AbstractEntity;

class CreateWorkspaceApp extends CreateSomething
{
    /** @var int */
    protected $scannerAppId;

    /**
     * CreateWorkspaceApp constructor.
     *
     * @param int $id
     * @param int $scannerAppId
     * @param AbstractEntity $entity
     * @param bool $multiMode
     */
    public function __construct(int $id, int $scannerAppId, AbstractEntity $entity, $multiMode = false)
    {
        parent::__construct($id, $entity, $multiMode);
        $this->scannerAppId = $scannerAppId;
    }

    /**
     * @return int
     */
    public function getScannerAppId(): int
    {
        return $this->scannerAppId;
    }
}