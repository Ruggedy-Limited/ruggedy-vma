<?php

namespace App\Handlers\Commands;

use App\Commands\GetListOfScannerApps as GetListOfScannerAppsCommand;
use App\Repositories\ScannerAppRepository;

class GetListOfScannerApps extends CommandHandler
{
    /** @var ScannerAppRepository */
    protected $repository;

    /**
     * GetListOfScannerApps constructor.
     *
     * @param ScannerAppRepository $repository
     */
    public function __construct(ScannerAppRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Process the GetListOfScannerApps command.
     *
     * @param GetListOfScannerAppsCommand $command
     * @return array
     */
    public function handle(GetListOfScannerAppsCommand $command)
    {
        return $this->repository->findAll();
    }
}