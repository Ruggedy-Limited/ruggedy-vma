<?php

namespace App\Handlers\Commands;

use App\Commands\GetListOfPermissions as GetListOfPermissionsCommand;
use App\Exceptions\ComponentNotFoundException;
use App\Exceptions\InvalidComponentEntityException;
use App\Exceptions\InvalidInputException;
use Exception;
use Illuminate\Support\Collection;


class GetListOfPermissions extends AbstractPermissionHandler
{
    /**
     * Process the GetListOfPermissions command
     *
     * @param GetListOfPermissionsCommand $command
     * @return Collection
     * @throws ComponentNotFoundException
     * @throws InvalidComponentEntityException
     * @throws InvalidInputException
     * @throws Exception
     */
    public function handle(GetListOfPermissionsCommand $command)
    {
        $id            = $command->getId();
        $componentName = $command->getComponentName();

        if (!isset($id, $componentName)) {
            throw new InvalidInputException("One or more required members are not set on the command");
        }

        // Fetch the component in order to get the component's Doctrine entity class
        $this->fetchAndSetComponent($componentName);

        // Fetch the component instance
        $this->fetchAndSetComponentInstance($id);

        $this->checkPermissions();

        // Get all the permissions for this component instance to return
        $componentInstancePermissions = $this->getComponentPermissionRepository()
            ->findByComponentAndComponentInstanceId(
                $this->getComponent()->getId(),
                $this->getComponentInstance()->getId()
            );

        if (empty($componentInstancePermissions)) {
            return new Collection();
        }

        return new Collection($componentInstancePermissions);
    }
}