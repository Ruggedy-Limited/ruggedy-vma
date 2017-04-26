<?php

namespace App\Handlers\Commands;

use App\Commands\GetFile as GetFileCommand;
use App\Entities\File;
use App\Entities\Vulnerability;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\FileNotFoundException;
use App\Policies\ComponentPolicy;
use App\Repositories\AssetRepository;
use App\Repositories\FileRepository;
use App\Repositories\VulnerabilityRepository;

class GetFile extends CommandHandler
{
    /** @var FileRepository */
    protected $fileRepository;

    /** @var VulnerabilityRepository */
    protected $vulnerabilityRepository;

    /** @var AssetRepository */
    protected $assetRepository;

    /**
     * GetFile constructor.
     *
     * @param FileRepository $fileRepository
     * @param VulnerabilityRepository $vulnerabilityRepository
     * @param AssetRepository $assetRepository
     */
    public function __construct(
        FileRepository $fileRepository, VulnerabilityRepository $vulnerabilityRepository,
        AssetRepository $assetRepository
    )
    {
        $this->fileRepository          = $fileRepository;
        $this->vulnerabilityRepository = $vulnerabilityRepository;
        $this->assetRepository         = $assetRepository;
    }

    /**
     * Process the GetFile command.
     *
     * @param GetFileCommand $command
     * @return array
     * @throws ActionNotPermittedException
     * @throws FileNotFoundException
     */
    public function handle(GetFileCommand $command)
    {
        $requestingUser = $this->authenticate();

        /** @var File $file */
        $file = $this->fileRepository->find($command->getId());
        if (empty($file)) {
            throw new FileNotFoundException("No file with the given ID was found.");
        }

        if ($requestingUser->cannot(ComponentPolicy::ACTION_VIEW, $file->getWorkspaceApp()->getWorkspace())) {
            throw new ActionNotPermittedException("The requesting User is not permitted to view this file.");
        }

        $vulnerabilities = $this->vulnerabilityRepository->findByFileQuery($command->getId());
        if (!$vulnerabilities->isEmpty()) {
            $vulnerabilities->getCollection()->transform(function ($result) {
                return current($result);
            });
        }

        return [
            Vulnerability::FILE   => $file,
            File::ASSETS          => $file->getAssets(),
            File::VULNERABILITIES => $vulnerabilities,
        ];
    }
}