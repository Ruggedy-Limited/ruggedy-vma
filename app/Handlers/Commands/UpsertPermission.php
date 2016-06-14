<?php

namespace App\Handlers\Commands;

use App\Commands\UpsertPermission as UpsertPermissionCommand;
use App\Entities\Component;
use App\Entities\ComponentPermission;
use App\Exceptions\ComponentNotFoundException;
use App\Exceptions\InvalidComponentEntityException;
use App\Exceptions\InvalidInputException;
use App\Exceptions\InvalidPermissionException;
use App\Exceptions\UserNotFoundException;
use App\Repositories\ComponentPermissionRepository;
use App\Repositories\ComponentRepository;
use App\Repositories\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Illuminate\Support\Collection;


class UpsertPermission extends CommandHandler
{
    /** @var ComponentRepository  */
    protected $componentRepository;

    /** @var ComponentPermissionRepository  */
    protected $componentPermissionRepository;

    /** @var UserRepository  */
    protected $userRepository;

    /** @var EntityManager  */
    protected $em;
    
    /** @var Collection */
    protected $validPermissions;

    /**
     * CreatePermission constructor.
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
        
        $this->validPermissions = new Collection([
            ComponentPermission::PERMISSION_READ_ONLY  => true,
            ComponentPermission::PERMISSION_READ_WRITE => true,
        ]);
    }

    /**
     * Process the CreatePermission command.
     *
     * @param UpsertPermissionCommand $command
     * @return Collection
     * @throws ComponentNotFoundException
     * @throws InvalidComponentEntityException
     * @throws InvalidInputException
     * @throws InvalidPermissionException
     * @throws UserNotFoundException
     * @throws \Exception
     */
    public function handle(UpsertPermissionCommand $command)
    {
        // Get the authenticated User
        $requestingUser = $this->authenticate();

        // Get all the required parameters from the command
        $id            = $command->getId();
        $componentName = $command->getComponentName();
        $userId        = $command->getUserId();
        $permission    = $command->getPermission();

        // Make sure all the required members were set on the command
        if (!isset($id, $componentName, $userId, $permission)) {
            throw new InvalidInputException("One or more required members are not set on the command");
        }

        // Get the formatted component name from the URL parameter
        $componentName = static::covertUrlParameterToComponentNameHelper($componentName);
        
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
        $user = $this->getUserRepository()->find($userId);
        if (empty($user)) {
            throw new UserNotFoundException("A User with that ID does not exist");
        }
        
        // Validate the permissions
        if (!$this->getValidPermissions()->contains($permission)) {
            throw new InvalidPermissionException("The given value for 'permission' is not a valid permission option");
        }

        // Create a new permission entity and set all the values
        $permissionEntity = new ComponentPermission();
        $permissionEntity->setComponent($component);
        $permissionEntity->setInstanceId($componentInstance->getId());
        $permissionEntity->setUserRelatedByUserId($user);
        $permissionEntity->setUserRelatedByGrantedBy($requestingUser);
        $permissionEntity->setPermission($permission);
        
        // Save the new permission to the database
        $this->getEm()->persist($permissionEntity);
        $this->getEm()->flush($permissionEntity);
        
        // Get all the permissions for this component instance to return
        $componentInstancePermissions = $this->getComponentPermissionRepository()
            ->findByComponentInstanceId($componentInstance->getId());

        // Create a collection containing the created permission and a Collection of all the permissions related to the
        // component instance
        $result = new Collection([
            ComponentPermission::RESULT_KEY_CREATED => $permissionEntity,
            ComponentPermission::RESULT_KEY_ALL     => $componentInstancePermissions,
        ]);
        
        return $result;
    }

    /**
     * Convert a URL slug with dashes into words with the first letter capitilised
     *
     * @param string $componentNameFromUrl
     * @return string
     */
    public static function covertUrlParameterToComponentNameHelper(string $componentNameFromUrl): string
    {
        if (empty($componentNameFromUrl)) {
            return '';
        }

        // Replace dashes with spaces and capitalise all the words
        return ucwords(str_replace("-", " ", $componentNameFromUrl));
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

    /**
     * @return Collection
     */
    public function getValidPermissions()
    {
        return $this->validPermissions;
    }
}