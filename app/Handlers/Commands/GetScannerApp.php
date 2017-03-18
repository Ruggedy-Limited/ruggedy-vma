<?php

namespace App\Handlers\Commands;

use App\Commands\GetScannerApp as GetScannerAppCommand;
use App\Exceptions\InvalidInputException;
use App\Exceptions\ScannerAppNotFoundException;
use App\Repositories\ScannerAppRepository;

class GetScannerApp extends CommandHandler
{
    /** @var ScannerAppRepository */
    protected $repository;

    /**
     * GetScannerApp constructor.
     *
     * @param ScannerAppRepository $repository
     */
    public function __construct(ScannerAppRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Process the GetScannerApp command.
     *
     * @param GetScannerAppCommand $command
     * @return null|object
     * @throws InvalidInputException
     * @throws ScannerAppNotFoundException
     */
    public function handle(GetScannerAppCommand $command)
    {
        $scannerAppId = $command->getId();
        if (!isset($scannerAppId)) {
            throw new InvalidInputException("One or more required members are not set on the command");
        }

        $scannerApp = $this->repository->find($scannerAppId);
        if (empty($scannerApp)) {
            throw new ScannerAppNotFoundException("There is no existing ScannerApp with the given ID");
        }

        return $scannerApp;
    }
}