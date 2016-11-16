<?php

namespace App\Http\Controllers\Api;

use App;
use App\Commands\CreateWorkspace;
use App\Commands\DeleteWorkspace;
use App\Commands\EditWorkspace;
use App\Commands\GetListOfUsersWorkspaces;
use App\Commands\GetWorkspace;
use App\Entities\Workspace;
use App\Services\EntityFactoryService;
use App\Transformers\WorkspaceTransformer;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;

/**
 * @Controller(prefix="api")
 * @Middleware("auth:api")
 */
class WorkspaceController extends AbstractController
{
    /**
     * Create a workspace on the given User's account
     *
     * @POST("/workspace/{userId}", as="workspace.create", where={"userId":"[0-9]+"})
     *
     * @param $userId
     * @return ResponseFactory|JsonResponse
     */
    public function createWorkspace($userId)
    {
        $workspace = EntityFactoryService::makeEntity(Workspace::class, $this->getRequest()->json()->all());
        $command = new CreateWorkspace(intval($userId), $workspace);
        return $this->sendCommandToBusHelper($command, new WorkspaceTransformer());
    }

    /**
     * Delete a workspace
     *
     * @DELETE("/workspace/{workspaceId}/{confirm?}", as="workspace.delete", where={"workspaceId":"[0-9]+", "confirm":"^confirm$"})
     *
     * @param $workspaceId
     * @param null $confirm
     * @return \Illuminate\Contracts\Routing\ResponseFactory
     */
    public function deleteWorkspace($workspaceId, $confirm = null)
    {
        $command = new DeleteWorkspace(intval($workspaceId), boolval($confirm));
        return $this->sendCommandToBusHelper($command, new WorkspaceTransformer());
    }

    /**
     * Edit Workspace Details
     *
     * @PUT("/workspace/{workspaceId}", as="workspace.edit", where={"workspaceId":"[0-9]+"})
     *
     * @param $workspaceId
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse
     */
    public function editWorkspace($workspaceId)
    {
        $command = new EditWorkspace(intval($workspaceId), $this->getRequest()->json()->all());
        return $this->sendCommandToBusHelper($command, new WorkspaceTransformer());
    }

    /**
     * Get a list of workspaces on a particular person's account
     *
     * @GET("/workspaces/{userId}", as="workspaces.list", where={"userId":"[0-9]+"})
     *
     * @param $userId
     * @return \Illuminate\Contracts\Routing\ResponseFactory
     */
    public function getWorkspacesForUser($userId)
    {
        $command  = new GetListOfUsersWorkspaces(intval($userId));
        return $this->sendCommandToBusHelper($command, new WorkspaceTransformer());
    }

    /**
     * Get a list of Vulnerabilities found in a particular Workspace
     *
     * @GET("/workspace/{workspaceId}", as="workspace.vulnerabilities.list", where={"workspaceId":"[0-9]+"})
     *
     * @param $workspaceId
     * @return \Illuminate\Contracts\Routing\ResponseFactory
     */
    public function getWorkspace($workspaceId)
    {
        $command = new GetWorkspace(intval($workspaceId));
        return $this->sendCommandToBusHelper($command, new WorkspaceTransformer());
    }

    /**
     * @inheritdoc
     */
    protected function getValidationRules(): array
    {
        return [
            'name' => 'bail|filled|alpha_num',
        ];
    }
}