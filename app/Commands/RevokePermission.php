<?php

namespace App\Commands;


class RevokePermission extends AbstractPermission
{
    /** @var int  */
    protected $userId;
    
    public function __construct(int $id, string $componentName, int $userId)
    {
        parent::__construct($id, $componentName);
        $this->userId = $userId;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }
}