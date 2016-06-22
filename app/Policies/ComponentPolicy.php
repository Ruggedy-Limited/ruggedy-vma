<?php

namespace App\Policies;

use App\Contracts\SystemComponent;
use App\Entities\ComponentPermission;
use App\Entities\User;
use App\Exceptions\RecursionLimitExceededException;
use App\Services\PermissionService;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Collection;


class ComponentPolicy
{
    use HandlesAuthorization;

    /**
     * Constant that relate to the authorisation methods related to this policy
     */
    const ACTION_CREATE = 'create';
    const ACTION_UPDATE = 'update';
    const ACTION_DELETE = 'delete';
    const ACTION_VIEW   = 'view';
    const ACTION_LIST   = 'list';
    
    const HIERARCHY_RECURSION_LIMIT = 5;
    
    protected $recursionCount;

    /**
     * Check if the User has permission to perform the requested action on the given component
     *
     * @param User $user
     * @param SystemComponent $component
     * @param Collection $requiredPermission
     * @return bool
     */
    protected function hasPermission(User $user, SystemComponent $component, Collection $requiredPermission)
    {
        if ($user->getId() === $component->getUser()->getId()) {
            return true;
        }

        $userHasPermission = $user->getPermissions()->exists(
            function ($offset, $permission) use ($component, $requiredPermission) {
                return $this->iterateComponentHierarchy($component, $permission, $requiredPermission);
            }
        );

        if (empty($user->getTeam())) {
            return $userHasPermission;
        }

        $teamHasPermissions = $user->getTeam()->getPermissions()->exists(
            function ($offset, $permission) use ($component, $requiredPermission) {
                return $this->iterateComponentHierarchy($component, $permission, $requiredPermission);
            }
        );

        return $userHasPermission || $teamHasPermissions;
    }

    /**
     * Iterate over the component hierarchy and check for the relevant permissions
     *
     * @param SystemComponent $component
     * @param ComponentPermission $permission
     * @param Collection $requiredPermission
     * @return bool
     */
    protected function iterateComponentHierarchy(
        SystemComponent $component, ComponentPermission $permission, Collection $requiredPermission
    )
    {
        $this->setRecursionCount(0);

        try {
            return $this->checkForComponentPermission($component, $permission, $requiredPermission);
        } catch (RecursionLimitExceededException $e) {
            return false;
        }
    }

    /**
     * Check for the relevant permissions for this component
     *
     * @param SystemComponent $component
     * @param ComponentPermission $permission
     * @param Collection $requiredPermission
     * @return bool
     * @throws RecursionLimitExceededException
     */
    protected function checkForComponentPermission(
        SystemComponent $component, ComponentPermission $permission, Collection $requiredPermission
    )
    {
        // Break out of the loop if we've hit the maximum recursion limit
        if ($this->getRecursionCount() >= self::HIERARCHY_RECURSION_LIMIT) {
            throw new RecursionLimitExceededException(
                "The recursion limit of " . self::HIERARCHY_RECURSION_LIMIT . " was exceeded when iterating over the"
                . " component permission hierarchy"
            );
        }
        
        $componentClass = PermissionService::covertEntityClassToComponentNameHelper($component);

        if ($permission->getComponent()->getClassName() === $componentClass
            && $permission->getInstanceId() === $component->getId()
            && $requiredPermission->contains($permission->getPermission())) {
            return true;
        }
        
        $parent = $component->getParent();
        while (!empty($parent)) {
            $this->iterateRecursionCount();
            if ($this->checkForComponentPermission($parent, $permission, $requiredPermission)) {
                return true;
            }

            $parent = $parent->getParent();
        }

        return false;
    }

    /**
     * Check for write permission
     *
     * @param User $userOrTeam
     * @param SystemComponent $component
     * @return bool
     */
    protected function write(User $userOrTeam, SystemComponent $component)
    {
        return $this->hasPermission($userOrTeam, $component, new Collection(
            [ComponentPermission::PERMISSION_READ_WRITE]
        ));
    }

    /**
     * Check for read permission
     *
     * @param User $userOrTeam
     * @param SystemComponent $component
     * @return bool
     */
    protected function read(User $userOrTeam, SystemComponent $component)
    {
        return $this->hasPermission($userOrTeam, $component, new Collection([
            ComponentPermission::PERMISSION_READ_ONLY,
            ComponentPermission::PERMISSION_READ_WRITE,
        ]));
    }

    /**
     * Check for create permission
     *
     * @param User $userOrTeam
     * @param SystemComponent $component
     * @return bool
     */
    public function create(User $userOrTeam, SystemComponent $component)
    {
        return $this->write($userOrTeam, $component);
    }

    /**
     * Check for update/edit permission
     *
     * @param User $userOrTeam
     * @param SystemComponent $component
     * @return bool
     */
    public function update(User $userOrTeam, SystemComponent $component)
    {
        return $this->write($userOrTeam, $component);
    }

    /**
     * Check for update/edit permission
     *
     * @param User $userOrTeam
     * @param SystemComponent $component
     * @return bool
     */
    public function edit(User $userOrTeam, SystemComponent $component)
    {
        return $this->write($userOrTeam, $component);
    }

    /**
     * Check for delete permission
     *
     * @param User $userOrTeam
     * @param SystemComponent $component
     * @return bool
     */
    public function delete(User $userOrTeam, SystemComponent $component)
    {
        return $this->write($userOrTeam, $component);
    }

    /**
     * Check for view permission
     *
     * @param User $userOrTeam
     * @param SystemComponent $component
     * @return bool
     */
    public function view(User $userOrTeam, SystemComponent $component)
    {
        return $this->read($userOrTeam, $component);
    }

    /**
     * Check for list permission
     *
     * @param User $userOrTeam
     * @param SystemComponent $component
     * @return bool
     */
    public function list(User $userOrTeam, SystemComponent $component)
    {
        return $this->read($userOrTeam, $component);
    }

    /**
     * @return int
     */
    public function getRecursionCount()
    {
        return $this->recursionCount;
    }

    /**
     * @param int $recursionCount
     */
    public function setRecursionCount($recursionCount)
    {
        $this->recursionCount = $recursionCount;
    }

    /**
     * Iterate the recursion counter
     */
    protected function iterateRecursionCount()
    {
        $this->recursionCount++;
    }
}
