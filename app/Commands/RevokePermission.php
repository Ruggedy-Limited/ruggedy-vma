<?php

namespace App\Commands;


class RevokePermission extends AbstractPermission
{
    /** @var int  */
    protected $userId;

    /**
     * RevokePermission constructor.
     * 
     * @param int $id
     * @param string $componentName
     * @param int $userId
     */
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