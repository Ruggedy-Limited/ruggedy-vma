<?php

namespace App\Handlers\Commands;

use App\Commands\RevokePermission as RevokePermissionCommand;
use App\Entities\Component;
use App\Entities\ComponentPermission;
use App\Entities\User;
use App\Exceptions\ComponentNotFoundException;
use App\Exceptions\InvalidComponentEntityException;
use App\Exceptions\InvalidInputException;
use App\Exceptions\UserNotFoundException;
use App\Repositories\ComponentPermissionRepository;
use App\Repositories\ComponentRepository;
use App\Repositories\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Illuminate\Support\Collection;


class RevokePermission extends CommandHandler
{
    /** @var ComponentRepository  */
    protected $componentRepository;

    /** @var ComponentPermissionRepository  */
    protected $componentPermissionRepository;

    /** @var UserRepository  */
    protected $userRepository;

    /** @var EntityManager  */
    protected $em;

    /**
     * RevokePermission constructor.
     *
     * @param ComponentRepository $componentRepository
     * @param ComponentPermissionRepository $componentPermissionRepository
     * @param UserRepository $userRepository
     * @param EntityManager $em
     */
    public function __construct(
        ComponentRepository $componentRepository, ComponentPermissionRepository $componentPermissionRepository,
        UserRepository $userRepository, EntityManager $em
    )
    {
        $this->componentRepository           = $componentRepository;
        $this->componentPermissionRepository = $componentPermissionRepository;
        $this->userRepository                = $userRepository;
        $this->em                            = $em;
    }

    /**
     * Process the RevokePermission command
     *
     * @param RevokePermissionCommand $command
     * @return Collection
     * @throws ComponentNotFoundException
     * @throws InvalidComponentEntityException
     * @throws InvalidInputException
     * @throws \Exception
     */
    public function handle(RevokePermissionCommand $command)
    {
        $requestingUser = $this->authenticate();

        $id            = $command->getId();
        $componentName = $command->getComponentName();
        $userId        = $command->getUserId();

        if (!isset($id, $componentName, $userId)) {
            throw new InvalidInputException("One or more required members are not set on the command");
        }

        // Get the formatted component name from the URL parameter
        $componentName = UpsertPermission::covertUrlParameterToComponentNameHelper($componentName);

        // Get the component in order to get the component's Doctrine entity class
        /** @var Component $component */
        $component = $this->getComponentRepository()->findByComponentName($componentName);
        if (empty($component)) {
            throw new ComponentNotFoundException("The given component name is not valid");
        }

        // Get an EntityRepository for the component instance
        $entityClass      = $component->getClassName();
        $entityRepository = $this->getEm()->getRepository("App\\Entities\\" . $entityClass);
        if (empty($entityRepository) || !($entityRepository instanceof EntityRepository)) {
            throw new InvalidComponentEntityException("Could not create an EntityRepository"
                . " from the entity class name");
        }

        // Get the component instance
        $componentInstance = $entityRepository->find($id);
        if (empty($componentInstance)) {
            throw new ComponentNotFoundException("That {$component->getName()} does not exist");
        }

        // Get the User that the permissions are being created for
        /** @var User $user */
        $user = $this->getUserRepository()->find($userId);
        if (empty($user)) {
            throw new UserNotFoundException("A User with that ID does not exist");
        }

        // Get all the permissions for this component instance to return
        $componentInstancePermissions = $this->getComponentPermissionRepository()
            ->findByComponentInstanceId($componentInstance->getId());

        $permissionEntity = $this->getComponentPermissionRepository()
            ->findByComponentInstanceAndUserIds($component->getId(), $id, $user->getId());
        
        if (empty($permissionEntity)) {
            // Create a new permission entity and set all the values
            $permissionEntity = new ComponentPermission();
            $permissionEntity->setComponent($component);
            $permissionEntity->setInstanceId($componentInstance->getId());
            $permissionEntity->setUserRelatedByUserId($user);
            $permissionEntity->setUserRelatedByGrantedBy($requestingUser);
        }

        $this->getEm()->remove($permissionEntity);
        $this->getEm()->flush($permissionEntity);

        return new Collection([
            ComponentPermission::RESULT_KEY_DELETED => $permissionEntity,
            ComponentPermission::RESULT_KEY_ALL     => $componentInstancePermissions,
        ]);
    }

    /**
     * @return ComponentRepository
     */
    public function getComponentRepository()
    {
        return $this->componentRepository;
    }

    /**
     * @return ComponentPermissionRepository
     */
    public function getComponentPermissionRepository()
    {
        return $this->componentPermissionRepository;
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