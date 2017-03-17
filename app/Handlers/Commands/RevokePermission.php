<?php

namespace App\Handlers\Commands;

use App\Commands\RevokePermission as RevokePermissionCommand;
use App\Entities\ComponentPermission;
use App\Exceptions\ComponentNotFoundException;
use App\Exceptions\InvalidComponentEntityException;
use App\Exceptions\InvalidInputException;
use Exception;
use Illuminate\Support\Collection;


class RevokePermission extends AbstractPermissionHandler
{
    /**
     * Process the RevokePermission command
     *
     * @param RevokePermissionCommand $command
     * @return Collection
     * @throws ComponentNotFoundException
     * @throws InvalidComponentEntityException
     * @throws InvalidInputException
     * @throws Exception
     */
    public function handle(RevokePermissionCommand $command)
    {
        $id            = $command->getId();
        $componentName = $command->getComponentName();
        $userId        = $command->getUserId();

        if (!isset($id, $componentName, $userId)) {
            throw new InvalidInputException("One or more required members are not set on the command");
        }

        // Fetch the component in order to get the component's Doctrine entity class
        $this->service->initialise($componentName, $id, $userId);

        $permissionEntity = $this->findOrCreatePermissionEntity($id);

        // No existing permission was found for the given user on the given component, so we return a success
        // result without making any changes to the database because there is nothing to change. This is the
        // only case were $permissionEntity->getPermission() should return null.
        if (!empty($permissionEntity->getPermission())) {
            // Save changes to the database
            $this->em->remove($permissionEntity);
            $this->em->flush($permissionEntity);
        }

        // Get all the permissions for this component instance to return
        $componentInstancePermissions = $this->service->getPermissionsByComponentAndComponentInstanceId();

        return new Collection([
            ComponentPermission::RESULT_KEY_AFFECTED => $permissionEntity,
            ComponentPermission::RESULT_KEY_ALL      => $componentInstancePermissions,
        ]);
    }
}