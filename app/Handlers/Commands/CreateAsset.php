<?php

namespace App\Handlers\Commands;

use App\Commands\CreateAsset as CreateAssetCommand;
use App\Entities\Asset;
use App\Exceptions\InvalidInputException;
use App\Exceptions\UserNotFoundException;
use App\Exceptions\WorkspaceNotFoundException;
use App\Repositories\UserRepository;
use App\Repositories\WorkspaceRepository;
use Doctrine\ORM\EntityManager;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;


class CreateAsset extends CommandHandler
{
    /** @var WorkspaceRepository */
    protected $workspaceRepository;

    /** @var UserRepository */
    protected $userRepository;

    /** @var EntityManager */
    protected $em;

    /** @var Collection */
    protected $validDetailAttributes;

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

        $this->validDetailAttributes = new Collection([
            'cpe', 'vendorName', 'ipV4', 'ipV6', 'hostname', 'macAddress', 'osVersion',
        ]);
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
        $requestingUser = $this->authenticate();
        
        $name        = $command->getName();
        $workspaceId = $command->getWorkspaceId();
        $userId      = $command->getUserId();
        $details     = $command->getDetails();
        if (!isset($name, $workspaceId, $userId, $details)) {
            throw new InvalidInputException("One or more required members are not set on the command");
        }
        
        $workspace = $this->getWorkspaceRepository()->find($workspaceId);
        if (empty($workspace)) {
            throw new WorkspaceNotFoundException("No Workspace with the given ID was found in the database");
        }
        
        $user = $this->getUserRepository()->find($userId);
        if (empty($user)) {
            throw new UserNotFoundException("No User with the given user ID was found in the database");
        }

        $details = $this->getValidDetails($details);
        
        $asset = new Asset();
        $asset->setName($name);
        $asset->setWorkspace($workspace);
        $asset->setUser($user);
        $asset->setFromArray($details);

        $this->getEm()->persist($asset);
        $this->getEm()->flush($asset);

        return $asset;
    }

    /**
     * Get valid details from the details array provided with the command
     *
     * @param array $details
     * @return array
     */
    protected function getValidDetails(array $details): array
    {
        return $this->getValidDetailAttributes()->map(function($validatorClass, $detailName) use ($details)
        {
            if (empty($details[$detailName])) {
                return null;
            }

            if (!$this->validateDetail($validatorClass, $details[$detailName])) {
                return null;
            }

            return $details[$detailName];
        })->toArray();
    }

    /**
     * Validate a value given as part of the command details
     *
     * @param $validatorClass
     * @param $detailValue
     * @return bool
     */
    protected function validateDetail($validatorClass, $detailValue)
    {
        if (!isset($validatorClass)) {
            return true;
        }

        if (!isset($detailValue)) {
            return false;
        }

        $validator = App::make($validatorClass);
        return $validator->validate($detailValue);
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

    /**
     * @return Collection
     */
    public function getValidDetailAttributes()
    {
        return $this->validDetailAttributes;
    }
}