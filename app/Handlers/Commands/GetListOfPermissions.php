<?php

namespace App\Handlers\Commands;

use App\Commands\GetListOfPermissions as GetListOfPermissionsCommand;
use App\Entities\Component;
use App\Exceptions\ComponentNotFoundException;
use App\Exceptions\InvalidComponentEntityException;
use App\Exceptions\InvalidInputException;
use App\Repositories\ComponentPermissionRepository;
use App\Repositories\ComponentRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Illuminate\Support\Collection;


class GetListOfPermissions extends CommandHandler
{

    protected $componentRepository;

    /** @var ComponentPermissionRepository */
    protected $componentPermissionRepository;

    /** @var EntityManager */
    protected $em;

    /**
     * GetListOfPermissions constructor.
     *
     * @param ComponentPermissionRepository $componentPermissionRepository
     * @param ComponentRepository $componentRepository
     * @param EntityManager $em
     */
    public function __construct(
        ComponentPermissionRepository $componentPermissionRepository, ComponentRepository $componentRepository,
        EntityManager $em
    )
    {
        $this->componentPermissionRepository = $componentPermissionRepository;
        $this->componentRepository           = $componentRepository;
        $this->em                            = $em;
    }

    /**
     * Process the GetListOfPermissions command
     *
     * @param GetListOfPermissionsCommand $command
     * @return Collection
     * @throws ComponentNotFoundException
     * @throws InvalidComponentEntityException
     * @throws InvalidInputException
     * @throws \Exception
     */
    public function handle(GetListOfPermissionsCommand $command)
    {
        $requestingUser = $this->authenticate();

        $id            = $command->getId();
        $componentName = $command->getComponentName();

        if (!isset($id, $componentName)) {
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

        // Get all the permissions for this component instance to return
        $componentInstancePermissions = $this->getComponentPermissionRepository()
            ->findByComponentInstanceId($componentInstance->getId());

        if (empty($componentInstancePermissions)) {
            return new Collection();
        }

        return new Collection($componentInstancePermissions);
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
     * @return EntityManager
     */
    public function getEm()
    {
        return $this->em;
    }
}