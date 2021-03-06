<?php

namespace App\Handlers\Commands;

use App\Commands\UpsertPermission as UpsertPermissionCommand;
use App\Entities\ComponentPermission;
use App\Exceptions\ComponentNotFoundException;
use App\Exceptions\InvalidComponentEntityException;
use App\Exceptions\InvalidInputException;
use App\Exceptions\InvalidPermissionException;
use App\Exceptions\UserNotFoundException;
use Exception;
use Illuminate\Support\Collection;


class UpsertPermission extends AbstractPermissionHandler
{
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
     * @throws Exception
     */
    public function handle(UpsertPermissionCommand $command)
    {
        // Get all the required parameters from the command
        $id            = $command->getId();
        $componentName = $command->getComponentName();
        $userId        = $command->getUserId();
        $permission    = $command->getPermission();

        // Make sure all the required members were set on the command
        if (!isset($id, $componentName, $userId, $permission)) {
            throw new InvalidInputException("One or more required members are not set on the command");
        }

        $this->service->initialise($componentName, $id, $userId);

        // Validate the permissions
        if (!$this->service->getValidPermissions()->contains($permission)) {
            throw new InvalidPermissionException("The given value for 'permission' is not a valid permission option");
        }

        // Create a new permission entity and set all the values
        $permissionEntity = $this->findOrCreatePermissionEntity($id, $permission);
        
        // Save the new permission to the database
        $this->em->persist($permissionEntity);
        $this->em->flush($permissionEntity);
        
        // Get all the permissions for this component instance to return
        $componentInstancePermissions = $this->service->getPermissionsByComponentAndComponentInstanceId();

        // Create a collection containing the created permission and a Collection
        // of all the permissions related to the component instance
        return new Collection([
            ComponentPermission::RESULT_KEY_AFFECTED => $permissionEntity,
            ComponentPermission::RESULT_KEY_ALL      => $componentInstancePermissions,
        ]);
    }
}