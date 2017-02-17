<?php

namespace App\Handlers\Commands;

use App\Commands\DeleteWorkspaceApp as DeleteWorkspaceAppCommand;
use App\Entities\WorkspaceApp;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\InvalidInputException;
use App\Exceptions\WorkspaceAppNotFoundException;
use App\Policies\ComponentPolicy;
use App\Repositories\WorkspaceAppRepository;
use Doctrine\ORM\EntityManager;
use Exception;

class DeleteWorkspaceApp extends CommandHandler
{
    /** @var WorkspaceAppRepository */
    protected $workspaceAppRepository;

    /** @var EntityManager */
    protected $em;

    /**
     * DeleteWorkspaceApp constructor.
     * 
     * @param WorkspaceAppRepository $workspaceAppRepository
     * @param EntityManager $em
     */
    public function __construct(WorkspaceAppRepository $workspaceAppRepository, EntityManager $em)
    {
        $this->workspaceAppRepository = $workspaceAppRepository;
        $this->em                  = $em;
    }

    /**
     * Process the DeleteWorkspaceApp command
     *
     * @param DeleteWorkspaceAppCommand $command
     * @return WorkspaceApp
     * @throws ActionNotPermittedException
     * @throws Exception
     * @throws InvalidInputException
     * @throws WorkspaceAppNotFoundException
     */
    public function handle(DeleteWorkspaceAppCommand $command)
    {
        // Get the authenticated user
        $requestingUser = $this->authenticate();

        // Make sure that all the required members are set on the command
        $workspaceAppId = $command->getId();
        if (!isset($workspaceAppId)) {
            throw new InvalidInputException("The required ID member is not set on the command object");
        }

        // Check that the WorkspaceApp exists
        /** @var WorkspaceApp $workspaceApp */
        $workspaceApp = $this->workspaceAppRepository->find($workspaceAppId);
        if (empty($workspaceApp)) {
            throw new WorkspaceAppNotFoundException("A WorkspaceApp with the given ID was not found");
        }

        // Check that the User has permission to delete the WorkspaceApp
        if ($requestingUser->cannot(ComponentPolicy::ACTION_DELETE, $workspaceApp)) {
            throw new ActionNotPermittedException("User does not have permission to delete this WorkspaceApp");
        }

        // If the deletion has been confirmed, then delete the WorkspaceApp from the database
        if ($command->isConfirm()) {
            $this->em->remove($workspaceApp);
            $this->em->flush($workspaceApp);
        }

        return $workspaceApp;
    }
}