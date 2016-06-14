<?php

namespace App\Http\Controllers\Api;

use App\Commands\GetListOfPermissions;
use App\Commands\RevokePermission;
use App\Commands\UpsertPermission;


/**
 * @Controller(prefix="api")
 * @Middleware("auth:api")
 */
class PermissionController extends AbstractController
{
    /**
     * Create a new permission
     *
     * @POST('/acl/{componentName}/{componentInstanceId}', as="acl.create", where={"componentName":"[a-z_]+", "componentInstanceId":"[0-9]+"}
     * @PUT('/acl/{componentName}/{componentInstanceId}', as="acl.edit", where={"componentName":"[a-z_]+", "componentInstanceId":"[0-9]+"}
     *
     * @param $componentName
     * @param $componentInstanceId
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse
     */
    public function upsertPermission($componentName, $componentInstanceId)
    {
       $userId     = $this->getRequest()->json('userId', 0);
       $permission = $this->getRequest()->json('permission', '');

       $command = new UpsertPermission($componentInstanceId, $componentName, $userId, $permission);
       return $this->sendCommandToBusHelper($command);
    }

    /**
     * Delete a permission
     *
     * @DELETE('/acl/{componentName}/{componentInstanceId}/{userId}', as="acl.delete", where={"componentName":"[a-z_]+", "componentInstanceId":"[0-9]+", "userId":"[0-9]+"}
     *
     * @param $componentName
     * @param $componentInstanceId
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse
     */
    public function editPermission($componentName, $componentInstanceId, $userId)
    {
        $command = new RevokePermission($componentInstanceId, $componentName, $userId);
        return $this->sendCommandToBusHelper($command);
    }

    /**
     * Get the permissions related to a component instance
     *
     * @DELETE('/acl/{componentName}/{componentInstanceId}/{userId}', as="acl.delete", where={"componentName":"[a-z_]+", "componentInstanceId":"[0-9]+", "userId":"[0-9]+"}
     *
     * @param $componentName
     * @param $componentInstanceId
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse
     */
    public function getComponentPermissions($componentName, $componentInstanceId)
    {
        $command = new GetListOfPermissions($componentInstanceId, $componentName);
        return $this->sendCommandToBusHelper($command);
    }
}