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