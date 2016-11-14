<?php

namespace App\Http\Controllers\Api;

use App\Commands\GetListOfPermissions;
use App\Commands\RevokePermission;
use App\Commands\UpsertPermission;
use App\Entities\ComponentPermission;
use App\Transformers\ComponentPermissionChangesTransformer;
use App\Transformers\ComponentPermissionTransformer;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use League\Fractal\TransformerAbstract;


/**
 * @Controller(prefix="api/acl")
 * @Middleware("auth:api")
 */
class PermissionController extends AbstractController
{
    /**
     * Create a new permission
     *
     * @POST("/{componentName}/{componentInstanceId}", as="acl.create", where={"componentName":"[a-z_]+", "componentInstanceId":"[0-9]+"})
     * @PUT("/{componentName}/{componentInstanceId}", as="acl.edit", where={"componentName":"[a-z_]+", "componentInstanceId":"[0-9]+"})
     *
     * @param $componentName
     * @param $componentInstanceId
     * @return ResponseFactory|JsonResponse
     */
    public function upsertPermission($componentName, $componentInstanceId)
    {
       $userId     = $this->getRequest()->json('userId', 0);
       $permission = $this->getRequest()->json('permission', '');

       $command = new UpsertPermission($componentInstanceId, $componentName, $userId, $permission);
       return $this->sendCommandToBusHelper($command, new ComponentPermissionChangesTransformer());
    }

    /**
     * Delete a permission
     *
     * @DELETE("/{componentName}/{componentInstanceId}/{userId}", as="acl.delete", where={"componentName":"[a-z_]+", "componentInstanceId":"[0-9]+", "userId":"[0-9]+"})
     *
     * @param $componentName
     * @param $componentInstanceId
     * @return ResponseFactory|JsonResponse
     */
    public function revokePermission($componentName, $componentInstanceId, $userId)
    {
        $command = new RevokePermission($componentInstanceId, $componentName, $userId);
        return $this->sendCommandToBusHelper($command, new ComponentPermissionChangesTransformer());
    }

    /**
     * Get the permissions related to a component instance
     *
     * @GET("/{componentName}/{componentInstanceId}", as="acl.list", where={"componentName":"[a-z_]+", "componentInstanceId":"[0-9]+", "userId":"[0-9]+"})
     *
     * @param $componentName
     * @param $componentInstanceId
     * @return ResponseFactory|JsonResponse
     */
    public function getComponentPermissions($componentName, $componentInstanceId)
    {
        $command = new GetListOfPermissions($componentInstanceId, $componentName);
        return $this->sendCommandToBusHelper($command, new ComponentPermissionTransformer());
    }

    /**
     * @inheritdoc
     * @param $result
     * @param TransformerAbstract $transformer
     * @return string
     */
    protected function transformResult($result, TransformerAbstract $transformer): string
    {
        // Handle the Collection to be passed for ComponentPermission changes differently
        if ($result instanceof Collection && $transformer instanceof ComponentPermissionChangesTransformer) {
            return fractal()->item($result, $transformer)->toJson();
        }

        return parent::transformResult($result, $transformer);
    }

    /**
     * @inheritdoc
     */
    protected function getValidationRules(): array
    {
        return [
            'userId'     => 'bail|filled|integer|min:1',
            'permission' => [
                'bail',
                'filled',
                'regex:^(r|rw)$'
            ],
        ];
    }
}