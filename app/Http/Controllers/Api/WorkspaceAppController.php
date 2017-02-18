<?php

namespace App\Http\Controllers\Api;

use App\Commands\MoveFileToWorkspaceApp;
use App\Commands\CreateWorkspaceApp;
use App\Commands\DeleteWorkspaceApp;
use App\Commands\EditWorkspaceApp;
use App\Commands\GetWorkspaceApp;
use App\Entities\WorkspaceApp;
use App\Transformers\WorkspaceAppTransformer;

/**
 * @Controller(prefix="api")
 * @Middleware("auth:api")
 */
class WorkspaceAppController extends AbstractController
{
    /**
     * Get a single WorkspaceApp and various related data by using optional Fractal Transformer includes
     *
     * @GET("/workspace/app/{workspaceAppId}", as="workspace.app.get", where={"folderId":"[0-9]+"})
     *
     * @param $workspaceAppId
     * @return \Illuminate\Contracts\Routing\ResponseFactory
     */
    public function getWorkspaceApp($workspaceAppId)
    {
        $command = new GetWorkspaceApp(intval($workspaceAppId));
        return $this->sendCommandToBusHelper($command, new WorkspaceAppTransformer());
    }

    /**
     * Create a new WorkspaceApp
     *
     * @POST("/workspace/app/create/{workspaceId}/{scannerAppId}", as="workspace.app.create",
     *     where={"workspaceId":"[0-9]+", "scannerAppId":"[0-9]+"})
     *
     * @param $workspaceId
     * @param $scannerAppId
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse
     */
    public function createWorkspaceApp($workspaceId, $scannerAppId)
    {
        $workspaceApp = new WorkspaceApp();
        $workspaceApp->setFromArray($this->request->json()->all());
        $command = new CreateWorkspaceApp(intval($workspaceId), intval($scannerAppId), $workspaceApp);
        return $this->sendCommandToBusHelper($command, new WorkspaceAppTransformer());
    }

    /**
     * Edit an existing WorkspaceApp
     *
     * @PUT("/workspace/app/{workspaceAppId}", as="workspace.app.edit", where={"workspaceAppId":"[0-9]+"})
     *
     * @param $workspaceAppId
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse
     */
    public function editWorkspaceApp($workspaceAppId)
    {
        $command = new EditWorkspaceApp(intval($workspaceAppId), $this->request->json()->all());
        return $this->sendCommandToBusHelper($command, new WorkspaceAppTransformer());
    }

    /**
     * Delete an existing WorkspaceApp
     *
     * @DELETE("/workspace/app/{workspaceAppId}", as="workspace.app.delete", where={"workspaceAppId":"[0-9]+"})
     *
     * @param $workspaceAppId
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse
     */
    public function deleteWorkspaceApp($workspaceAppId)
    {
        $command = new DeleteWorkspaceApp(intval($workspaceAppId), true);
        return $this->sendCommandToBusHelper($command, new WorkspaceAppTransformer());
    }

    /**
     * Move a File to a different WorkspaceApp
     *
     * @PUT("/workspace/app/{workspaceAppId}/{fileId}", as="workspace.app.add.vulnerability",
     *     where={"workspaceAppId":"[0-9]+", "fileId":"[0-9]+"})
     *
     * @param $workspaceAppId
     * @param $fileId
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse
     */
    public function moveFileToWorkspaceApp($workspaceAppId, $fileId)
    {
        $command = new MoveFileToWorkspaceApp(intval($workspaceAppId), intval($fileId));
        return $this->sendCommandToBusHelper($command, new WorkspaceAppTransformer());
    }

    /**
     * @inheritdoc
     */
    protected function getValidationRules(): array
    {
        return [
            'name'        => 'bail|filled',
            'description' => 'bail|filled',
        ];
    }
}