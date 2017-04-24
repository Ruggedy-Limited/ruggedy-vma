<?php

namespace App\Services;

use App\Contracts\SystemComponent;
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
use Illuminate\Support\Collection;
use Auth;

class PermissionService
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

    /** @var Component */
    protected $component;

    /** @var SystemComponent */
    protected $componentInstance;

    /** @var Collection */
    protected $validPermissions;

    /**
     * AuthorisationService constructor.
     * 
     * @param ComponentPermissionRepository $componentPermissionRepository
     * @param ComponentRepository $componentRepository
     * @param UserRepository $userRepository
     * @param EntityManager $em
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

        $this->validPermissions = new Collection([
            ComponentPermission::PERMISSION_READ_ONLY,
            ComponentPermission::PERMISSION_READ_WRITE,
        ]);
    }

    /**
     * Initialise the service
     * 
     * @param string $componentName
     * @param int $id
     * @param int|null $userId
     * @throws ActionNotPermittedException
     * @throws ComponentNotFoundException
     * @throws InvalidComponentEntityException
     * @throws InvalidInputException
     * @throws UserNotFoundException
     */
    public function initialise(string $componentName, int $id, int $userId = null)
    {
        // Fetch the component in order to get the component's Doctrine entity class
        $this->fetchAndSetComponent($componentName);

        // Fetch the component instance
        $this->fetchAndSetComponentInstance($id);

        $this->checkPermissions();

        // Fetch the User that the permissions are being created for
        if (isset($userId)) {
            $this->fetchAndSetUser($userId);
        }
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
        $component = $this->componentRepository->findOneByComponentName($componentName);
        if (empty($component)) {
            throw new ComponentNotFoundException("The given component name is not valid");
        }

        $this->setComponent($component);
    }

    /**
     * Get the component instance and set it on the service
     *
     * @param int $id
     * @throws ComponentNotFoundException
     * @throws InvalidComponentEntityException
     * @throws InvalidInputException
     */
    protected function fetchAndSetComponentInstance(int $id)
    {
        $component = $this->component;
        if (empty($component) || !($component instanceof Component)) {
            throw new InvalidInputException("One or more required members were not set on the command handler");
        }

        // Get an EntityRepository for the component instance
        $entityClass      = $component->getClassName();
        $entityNamespace  = env('APP_MODEL_NAMESPACE');
        $entityRepository = $this->em->getRepository($entityNamespace . "\\" . $entityClass);
        if (empty($entityRepository) || !($entityRepository instanceof EntityRepository)) {
            throw new InvalidComponentEntityException("Could not create an EntityRepository"
                . " from the entity class name");
        }

        // Get the component instance
        /** @var SystemComponent $componentInstance */
        $componentInstance = $entityRepository->find($id);
        if (empty($componentInstance) || !($componentInstance instanceof SystemComponent)) {
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
        /** @var User $user */
        $user = Auth::user();

        /** @var User $componentOwner */
        $componentOwner = $this->componentInstance->getUser();
        if (empty($user) || $user->getId() !== $componentOwner->getId()) {
            throw new ActionNotPermittedException("The authenticated User is not the owner and cannot set permissions");
        }

        return true;
    }

    /**
     * Get the User to apply to permission changes to and set it on the service
     *
     * @param int $userId
     * @throws UserNotFoundException
     */
    protected function fetchAndSetUser(int $userId)
    {
        // Get the User that the permissions are being created for
        /** @var User $user */
        $user = $this->userRepository->find($userId);
        if (empty($user) || $user->isDeleted()) {
            throw new UserNotFoundException("A User with that ID does not exist");
        }

        $this->setUser($user);
    }

    /**
     * Get permission for a particular component
     *
     * @return Collection
     */
    public function getPermissionsByComponentAndComponentInstanceId()
    {
        return $this->componentPermissionRepository->findByComponentAndComponentInstanceId(
            $this->component->getId(),
            $this->componentInstance->getId()
        );
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
     * Take a Doctrine Entity and return it's class name (excliuding class path, i.e. not fully qualified)
     *
     * @param SystemComponent $entity
     * @return string
     */
    public static function convertEntityClassToComponentNameHelper(SystemComponent $entity): string
    {
        $className = get_class($entity);
        if (strpos($className, "\\") !== false) {
            $classNameParts = explode("\\", $className);
            $className = array_pop($classNameParts);
        }

        return $className;
    }

    /**
     * @return ComponentRepository
     */
    public function getComponentRepository()
    {
        return $this->componentRepository;
    }

    /**
     * @param ComponentRepository $componentRepository
     */
    public function setComponentRepository($componentRepository)
    {
        $this->componentRepository = $componentRepository;
    }

    /**
     * @return ComponentPermissionRepository
     */
    public function getComponentPermissionRepository()
    {
        return $this->componentPermissionRepository;
    }

    /**
     * @param ComponentPermissionRepository $componentPermissionRepository
     */
    public function setComponentPermissionRepository($componentPermissionRepository)
    {
        $this->componentPermissionRepository = $componentPermissionRepository;
    }

    /**
     * @return UserRepository
     */
    public function getUserRepository()
    {
        return $this->userRepository;
    }

    /**
     * @param UserRepository $userRepository
     */
    public function setUserRepository($userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @return EntityManager
     */
    public function getEm()
    {
        return $this->em;
    }

    /**
     * @param EntityManager $em
     */
    public function setEm($em)
    {
        $this->em = $em;
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
     * @return SystemComponent
     */
    public function getComponentInstance()
    {
        return $this->componentInstance;
    }

    /**
     * @param SystemComponent $componentInstance
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

    /**
     * @param Collection $validPermissions
     */
    public function setValidPermissions($validPermissions)
    {
        $this->validPermissions = $validPermissions;
    }
}