<?php

namespace App\Commands;


class UpsertPermission extends RevokePermission
{
    /** @var string */
    protected $permission;

    /**
     * UpsertPermission constructor.
     *
     * @param int $id
     * @param string $componentName
     * @param int $userId
     * @param string $permission
     */
    public function __construct(int $id, string $componentName, int $userId, string $permission)
    {
        parent::__construct($id, $componentName, $userId);
        $this->permission = $permission;
    }

    /**
     * @return string
     */
    public function getPermission()
    {
        return $this->permission;
    }
}