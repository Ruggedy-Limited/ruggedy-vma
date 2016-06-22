<?php

namespace App\Handlers\Commands;

use App\Entities\ComponentPermission;
use App\Entities\User;
use App\Exceptions\InvalidInputException;
use App\Services\PermissionService;
use Doctrine\ORM\EntityManager;
use Exception;


abstract class AbstractPermissionHandler extends CommandHandler
{
    /** @var EntityManager */
    protected $em;

    /** @var User */
    protected $authenticatedUser;

    /** @var PermissionService */
    protected $service;

    /**
     * AbstractPermissionHandler constructor.
     *
     * @param PermissionService $service
     * @param EntityManager $em
     * @throws Exception
     */
    public function __construct(PermissionService $service, EntityManager $em)
    {
        $this->service           = $service;
        $this->em                = $em;
        $this->authenticatedUser = $this->authenticate();
    }

    /**
     * Get the component record and set it on the handler
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
     * Get the component instance and set it on the handler
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
        $entityNamespace  = env('APP_MODEL_NAMESPACE');
        $entityRepository = $this->getEm()->getRepository($entityNamespace . "\\" . $entityClass);
        if (empty($entityRepository) || !($entityRepository instanceof EntityRepository)) {
            throw new InvalidComponentEntityException("Could not create an EntityRepository"
                . " from the entity class name");
        }

        // Get the component instance
        /** @var HasGetId $componentInstance */
        $componentInstance = $entityRepository->find($id);
        if (empty($componentInstance) || !($componentInstance instanceof HasGetId)) {
            $exceptionNamespace = env('APP_EXCEPTION_NAMESPACE');
            $exceptionClass = $exceptionNamespace . "\\" . $entityClass . "NotFoundException";
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
     * Get the User to apply to permission changes to and set it on the handler
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
        $component         = $this->getService()->getComponent();
        $componentInstance = $this->getService()->getComponentInstance();
        $user              = $this->getService()->getUser();
        $requestingUser    = $this->getAuthenticatedUser();

        // Check that the command handler state is set as required
        if (!isset($component, $componentInstance, $user, $requestingUser)) {
            throw new InvalidInputException("One or more required members were not set on the command handler");
        }

        /** @var ComponentPermission $permissionEntity */
        $permissionEntity = $this->getService()
            ->getComponentPermissionRepository()
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
     * @return EntityManager
     */
    public function getEm()
    {
        return $this->em;
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
     * @return PermissionService
     */
    public function getService()
    {
        return $this->service;
    }
}