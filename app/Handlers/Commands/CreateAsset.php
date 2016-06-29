<?php

namespace App\Handlers\Commands;

use App\Commands\CreateAsset as CreateAssetCommand;
use App\Entities\Asset;
use App\Entities\Workspace;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\InvalidInputException;
use App\Exceptions\UserNotFoundException;
use App\Exceptions\WorkspaceNotFoundException;
use App\Policies\ComponentPolicy;
use App\Repositories\UserRepository;
use App\Repositories\WorkspaceRepository;
use Doctrine\ORM\EntityManager;
use Exception;

class CreateAsset extends CommandHandler
{
    /** @var WorkspaceRepository */
    protected $workspaceRepository;

    /** @var UserRepository */
    protected $userRepository;

    /** @var EntityManager */
    protected $em;

    /**
     * CreateAsset constructor.
     *
     * @param WorkspaceRepository $workspaceRepository
     * @param UserRepository $userRepository
     * @param EntityManager $em
     */
    public function __construct(WorkspaceRepository $workspaceRepository, UserRepository $userRepository, EntityManager $em)
    {
        $this->workspaceRepository = $workspaceRepository;
        $this->userRepository      = $userRepository;
        $this->em                  = $em;
    }

    /**
     * Process the CreateAsset command.
     *
     * @param CreateAssetCommand $command
     * @return Asset
     * @throws InvalidInputException
     * @throws UserNotFoundException
     * @throws WorkspaceNotFoundException
     * @throws Exception
     */
    public function handle(CreateAssetCommand $command)
    {
        // Get the authenticated User
        $requestingUser = $this->authenticate();
        
        // Check that all the required fields were set on the command
        $workspaceId = $command->getId();
        $details     = $command->getDetails();
        if (!isset($workspaceId, $details)) {
            throw new InvalidInputException("One or more required members are not set on the command");
        }

        // Make sure the given Workspace exists
        /** @var Workspace $workspace */
        $workspace = $this->getWorkspaceRepository()->find($workspaceId);
        if (empty($workspace)) {
            throw new WorkspaceNotFoundException("No Workspace with the given ID was found in the database");
        }

        // Make sure the authenticated User has permission to add an Asset to this Workspace
        if ($requestingUser->cannot(ComponentPolicy::ACTION_CREATE, $workspace)) {
            throw new ActionNotPermittedException(
                "The authenticated User does not have permission to create an Asset on the given Workspace"
            );
        }

        // Create a new Asset
        $asset = new Asset();
        $asset->setWorkspace($workspace);
        $asset->setUser($workspace->getUser());
        $asset->setFromArray($details);

        // Persist the new Asset to the database
        $this->getEm()->persist($asset);
        $this->getEm()->flush($asset);

        return $asset;
    }

    /**
     * @return WorkspaceRepository
     */
    public function getWorkspaceRepository()
    {
        return $this->workspaceRepository;
    }

    /**
     * @return UserRepository
     */
    public function getUserRepository()
    {
        return $this->userRepository;
    }

    /**
     * @return EntityManager
     */
    public function getEm()
    {
        return $this->em;
    }
}