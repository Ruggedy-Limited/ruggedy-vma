<?php

namespace App\Handlers\Commands;

use App\Contracts\HasGetId;
use App\Contracts\HasOwnerUserEntity;
use App\Entities\Base\AbstractEntity;
use App\Entities\Component;
use App\Entities\ComponentPermission;
use App\Entities\User;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\ComponentNotFoundException;
use App\Exceptions\InvalidComponentEntityException;
use App\Exceptions\InvalidInputException;
use App\Exceptions\UserNotFoundException;
use App\Repositories\ComponentPermissionRepository;
use App\Repositories\ComponentRepository;
use App\Repositories\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Exception;
use Illuminate\Support\Collection;


abstract class AbstractPermissionHandler extends CommandHandler
{
    /** @var ComponentRepository */
    protected $componentRepository;

    /** @var ComponentPermissionRepository */
    protected $componentPermissionRepository;

    /** @var UserRepository */
    protected $userRepository;

    /** @var EntityManager */
    protected $em;

    /** @var User */
    protected $user;

    /** @var User */
    protected $authenticatedUser;

    /** @var Component */
    protected $component;

    /** @var AbstractEntity */
    protected $componentInstance;

    /** @var Collection */
    protected $validPermissions;

    /**
     * GetListOfPermissions constructor.
     *
     * @param ComponentPermissionRepository $componentPermissionRepository
     * @param ComponentRepository $componentRepository
     * @param UserRepository $userRepository
     * @param EntityManager $em
     * @throws Exception
     */
    public function __construct(
        ComponentPermissionRepository $componentPermissionRepository, ComponentRepository $componentRepository,
        UserRepository $userRepository, EntityManager $em
    )
    {
        $this->componentPermissionRepository = $componentPermissionRepository;
        $this->componentRepository           = $componentRepository;
        $this->userRepository                = $userRepository;
        $this->em                            = $em;
        $this->authenticatedUser             = $this->authenticate();

        $this->validPermissions = new Collection([
            ComponentPermission::PERMISSION_READ_ONLY,
            ComponentPermission::PERMISSION_READ_WRITE,
        ]);
    }

    /**
     * Get the component record
     *
     * @param string $componentName
     * @throws ComponentNotFoundException
     */
    protected function fetchAndSetComponent(string $componentName)
    {
        // Get the formatted component name from the URL parameter
        $componentName = static::covertUrlParameterToComponentNameHelper($componentName);

        // Get the component in order to get the component's Doctrine entity class
        /** @var Component $component */
        $component = $this->getComponentRepository()->findOneByComponentName($componentName);
        if (empty($component)) {
            throw new ComponentNotFoundException("The given component name is not valid");
        }

        $this->setComponent($component);
    }

    /**
     * Get the component instance
     *
     * @param int $id
     * @throws ComponentNotFoundException
     * @throws InvalidComponentEntityException
     * @throws InvalidInputException
     */
    protected function fetchAndSetComponentInstance(int $id)
    {
        $component = $this->getComponent();
        if (empty($component) || !($component instanceof Component)) {
            throw new InvalidInputException("One or more required members were not set on the command handler");
        }

        // Get an EntityRepository for the component instance
        $entityClass      = $component->getClassName();
        $entityRepository = $this->getEm()->getRepository("App\\Entities\\" . $entityClass);
        if (empty($entityRepository) || !($entityRepository instanceof EntityRepository)) {
            throw new InvalidComponentEntityException("Could not create an EntityRepository"
                . " from the entity class name");
        }

        // Get the component instance
        /** @var HasGetId $componentInstance */
        $componentInstance = $entityRepository->find($id);
        if (empty($componentInstance) || !($componentInstance instanceof HasGetId)) {
            $exceptionClass = "App\\Exceptions\\" . $entityClass . "NotFoundException";
            $message        = "That {$component->getName()} does not exist";

            if (!class_exists($exceptionClass)) {
                throw new ComponentNotFoundException($message);
            }

            throw new $exceptionClass($message);

        }

        $this->setComponentInstance($componentInstance);
    }

    /**
     * Check if the authenticated user and the owner of the component are the same
     *
     * @return bool
     * @throws ActionNotPermittedException
     */
    protected function checkPermissions()
    {
        $user = $this->getAuthenticatedUser();
        /** @var User $componentOwner */
        $componentOwner = $this->getComponentInstance()->getUser();
        if ($user->getId() !== $componentOwner->getId()) {
            throw new ActionNotPermittedException("The authenticated User is not the owner and cannot set permissions");
        }

        return true;
    }

    /**
     * Get the User to apply to permission changes to
     *
     * @param int $userId
     * @throws UserNotFoundException
     */
    protected function fetchAndSetUser(int $userId)
    {
        // Get the User that the permissions are being created for
        /** @var User $user */
        $user = $this->getUserRepository()->find($userId);
        if (empty($user)) {
            throw new UserNotFoundException("A User with that ID does not exist");
        }

        $this->setUser($user);
    }

    /**
     * Get an existing permission if one with the relevant data can be found, otherwise create a new one
     *
     * @param int $id
     * @param string $permission
     * @return ComponentPermission
     * @throws InvalidInputException
     */
    protected function findOrCreatePermissionEntity(int $id, string $permission = null): ComponentPermission
    {
        $component         = $this->getComponent();
        $componentInstance = $this->getComponentInstance();
        $user              = $this->getUser();
        $requestingUser    = $this->getAuthenticatedUser();

        // Check that the command handler state is set as required
        if (!isset($component, $componentInstance, $user, $requestingUser)) {
            throw new InvalidInputException("One or more required members were not set on the command handler");
        }

        /** @var ComponentPermission $permissionEntity */
        $permissionEntity = $this->getComponentPermissionRepository()
            ->findOneByComponentInstanceAndUserIds($component->getId(), $id, $user->getId());

        if (empty($permissionEntity)) {
            // Create a new permission entity and set all the values
            $permissionEntity = new ComponentPermission();
            $permissionEntity->setComponent($component);
            $permissionEntity->setInstanceId($componentInstance->getId());
            $permissionEntity->setUserRelatedByUserId($user);
            $permissionEntity->setUserRelatedByGrantedBy($requestingUser);
        }

        // Change the permission to what was given if something was given
        if (!empty($permission)) {
            $permissionEntity->setPermission($permission);
        }

        return $permissionEntity;
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
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getAuthenticatedUser()
    {
        return $this->authenticatedUser;
    }

    /**
     * @param User $authenticatedUser
     */
    public function setAuthenticatedUser($authenticatedUser)
    {
        $this->authenticatedUser = $authenticatedUser;
    }

    /**
     * @return Component
     */
    public function getComponent()
    {
        return $this->component;
    }

    /**
     * @param Component $component
     */
    public function setComponent($component)
    {
        $this->component = $component;
    }

    /**
     * @return HasGetId|HasOwnerUserEntity
     */
    public function getComponentInstance()
    {
        return $this->componentInstance;
    }

    /**
     * @param HasGetId $componentInstance
     */
    public function setComponentInstance($componentInstance)
    {
        $this->componentInstance = $componentInstance;
    }

    /**
     * @return Collection
     */
    public function getValidPermissions()
    {
        return $this->validPermissions;
    }
}