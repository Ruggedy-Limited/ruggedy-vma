<?php

namespace App\Commands;

class GetAssets extends Command
{
    /** @var array */
    protected $assetIds;

    /**
     * GetAssets constructor.
     *
     * @param array $assetIds
     */
    public function __construct(array $assetIds)
    {
        $this->assetIds = $assetIds;
    }

    /**
     * @return array
     */
    public function getAssetIds(): array
    {
        return $this->assetIds;
    }
}