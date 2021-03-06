<?php

namespace App\Handlers\Commands;

use App\Commands\CreateWorkspaceApp as CreateWorkspaceAppCommand;
use App\Entities\File;
use App\Entities\ScannerApp;
use App\Entities\WorkspaceApp;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\InvalidInputException;
use App\Exceptions\ScannerAppNotFoundException;
use App\Exceptions\WorkspaceNotFoundException;
use App\Policies\ComponentPolicy;
use App\Repositories\ScannerAppRepository;
use App\Repositories\WorkspaceRepository;
use Doctrine\ORM\EntityManager;

class CreateWorkspaceApp extends CommandHandler
{
    /** @var WorkspaceRepository */
    protected $workspaceRepository;

    /** @var ScannerAppRepository */
    protected $scannerAppRepository;

    /** @var EntityManager */
    protected $em;

    /**
     * CreateWorkspaceApp constructor.
     *
     * @param WorkspaceRepository $workspaceRepository
     * @param ScannerAppRepository $scannerAppRepository
     * @param EntityManager $em
     */
    public function __construct(
        WorkspaceRepository $workspaceRepository, ScannerAppRepository $scannerAppRepository, EntityManager $em
    )
    {
        $this->workspaceRepository  = $workspaceRepository;
        $this->scannerAppRepository = $scannerAppRepository;
        $this->em                   = $em;
    }

    /**
     * Process the CreateWorkspaceApp command.
     *
     * @param CreateWorkspaceAppCommand $command
     * @return WorkspaceApp
     * @throws ActionNotPermittedException
     * @throws InvalidInputException
     * @throws ScannerAppNotFoundException
     * @throws WorkspaceNotFoundException
     */
    public function handle(CreateWorkspaceAppCommand $command)
    {
        $requestingUser = $this->authenticate();

        $workspaceId  = $command->getId();
        $scannerAppId = $command->getScannerAppId();
        /** @var WorkspaceApp $workspaceApp */
        $workspaceApp = $command->getEntity();

        // Make sure we have everything we need to process the command
        if (!isset($workspaceId, $scannerAppId, $workspaceApp)) {
            throw new InvalidInputException("One or more required members are not set on the command");
        }

        // Make sure the relevant Workspace exists
        $workspace = $this->workspaceRepository->find($workspaceId);
        if (empty($workspace)) {
            throw new WorkspaceNotFoundException("No Workspace with the given ID was found.");
        }

        $scannerApp = $this->scannerAppRepository->find($scannerAppId);
        // Make sure the relevant ScannerApp exists
        if (empty($scannerApp)) {
            throw new ScannerAppNotFoundException("No Scanner App with the given ID was found.");
        }

        // Make sure the requesting user has permission to perform this action
        if ($requestingUser->cannot(ComponentPolicy::ACTION_CREATE, $workspace)) {
            throw new ActionNotPermittedException(
                "The requesting User does not have permission to create Folders on the given Workspace"
            );
        }

        // Set the Workspace, ScannerApp and User on the WorkspaceApp
        $workspaceApp->setWorkspace($workspace);
        $workspaceApp->setScannerApp($scannerApp);
        $workspaceApp->setUser($requestingUser);

        // Persist the changes to the database and refresh the entity state
        $this->em->persist($workspaceApp);
        $this->em->flush($workspaceApp);

        // Create a dummy file for Ruggedy App
        if ($scannerApp->getName() === ScannerApp::SCANNER_RUGGEDY) {
            $file = new File();
            $file->setName($workspaceApp->getName())
                ->setDescription($workspaceApp->getDescription())
                ->setFormat(File::FILE_TYPE_XML)
                ->setPath(base_path())
                ->setDeleted(false)
                ->setProcessed(true)
                ->setSize(0)
                ->setUser($requestingUser)
                ->setWorkspaceApp($workspaceApp);

            $this->em->persist($file);
            $this->em->flush($file);
        }

        $this->em->refresh($workspaceApp);
        return $workspaceApp;
    }
}