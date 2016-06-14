<?php

namespace App\Commands;


abstract class AbstractPermission extends Command
{
    /** @var int  */
    protected $id;

    /** @var string  */
    protected $componentName;

    /**
     * RevokePermission constructor.
     *
     * @param int $id
     * @param string $componentName
     */
    public function __construct(int $id, string $componentName)
    {
        $this->id            = $id;
        $this->componentName = $componentName;
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
    public function getComponentName()
    {
        return $this->componentName;
    }
}