<?php

namespace App\Http\Controllers\Api;

use App;
use App\Commands\CreateWorkspace;
use App\Commands\DeleteWorkspace;
use App\Commands\EditWorkspace;
use App\Team as EloquentTeam;
use App\User as EloquentUser;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Laravel\Spark\Interactions\Settings\Teams\SendInvitation;


/**
 * @Controller(prefix="api")
 * @Middleware("auth:api")
 */
class WorkspaceController extends AbstractController
{
    /**
     * Create a workspace in the given project
     *
     * @POST("/workspace/{projectId}", as="workspace.create", where={"projectId":"[0-9]+"})
     *
     * @param $projectId
     * @return ResponseFactory|JsonResponse
     */
    public function createWorkspace($projectId)
    {
        $command = new CreateWorkspace($projectId, $this->getRequest()->json()->all());
        return $this->sendCommandToBusHelper($command);
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
        return $this->sendCommandToBusHelper($command);
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
        return $this->sendCommandToBusHelper($command);
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
        return $this->sendCommandToBusHelper($command);
    }

    /**
     * @inheritdoc
     */
    protected function getValidationRules(): array
    {
        return [
            'name'       => 'bail|filled|alpha_num',
        ];
    }
}