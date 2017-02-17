<?php

namespace App\Handlers\Commands;

use App\Commands\EditWorkspaceApp as EditWorkspaceAppCommand;
use App\Entities\WorkspaceApp;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\InvalidInputException;
use App\Exceptions\WorkspaceAppNotFoundException;
use App\Policies\ComponentPolicy;
use App\Repositories\WorkspaceAppRepository;
use Doctrine\ORM\EntityManager;

class EditWorkspaceApp extends CommandHandler
{
    /** @var WorkspaceAppRepository */
    protected $workspaceAppRepository;

    /** @var EntityManager */
    protected $em;

    /**
     * CreateWorkspaceApp constructor.
     *
     * @param WorkspaceAppRepository $workspaceAppRepository
     * @param EntityManager $em
     */
    public function __construct(WorkspaceAppRepository $workspaceAppRepository, EntityManager $em)
    {
        $this->workspaceAppRepository = $workspaceAppRepository;
        $this->em               = $em;
    }

    /**
     * Process the CreateWorkspaceApp command.
     *
     * @param EditWorkspaceAppCommand $command
     * @return WorkspaceApp
     * @throws ActionNotPermittedException
     * @throws InvalidInputException
     * @throws WorkspaceAppNotFoundException
     */
    public function handle(EditWorkspaceAppCommand $command)
    {
        $requestingUser = $this->authenticate();

        $workspaceAppId      = $command->getId();
        $workspaceAppDetails = $command->getRequestedChanges();
        // Make sure we have everything we need to process the command
        if (!isset($workspaceAppId, $workspaceAppDetails)) {
            throw new InvalidInputException("One or more required members are not set on the command");
        }

        // Make sure the relevant Workspace exists
        /** @var WorkspaceApp $workspaceApp */
        $workspaceApp = $this->workspaceAppRepository->find($workspaceAppId);
        if (empty($workspaceApp)) {
            throw new WorkspaceAppNotFoundException("No WorkspaceApp with the given ID was found.");
        }

        // Make sure the requesting user has permission to perform this action
        if ($requestingUser->cannot(ComponentPolicy::ACTION_EDIT, $workspaceApp)) {
            throw new ActionNotPermittedException(
                "The requesting User does not have permission to edit the given WorkspaceApp"
            );
        }

        // Set the amended details on the workspaceApp
        $workspaceApp->setFromArray($workspaceAppDetails);

        // Persist the changes to the database and refresh the entity state
        $this->em->persist($workspaceApp);
        $this->em->flush($workspaceApp);

        return $workspaceApp;
    }
}