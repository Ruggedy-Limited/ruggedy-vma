<?php

namespace App\Commands;

class AutoScanForNewFiles extends Command
{
    /** @var bool */
    protected $autoScan;

    /**
     * AutoScanForNewFIles constructor.
     *
     * @param bool $autoScan
     */
    public function __construct(bool $autoScan = false)
    {
        $this->autoScan = $autoScan;
    }

    /**
     * @return bool
     */
    public function isAutoScan(): bool
    {
        return $this->autoScan;
    }
}