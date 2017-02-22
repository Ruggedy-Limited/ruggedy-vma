<?php

namespace App\Http\Controllers\Api;

use App\Commands\CreateVulnerability;
use App\Commands\DeleteVulnerability;
use App\Commands\EditVulnerability;
use App\Commands\MoveFileToWorkspaceApp;
use App\Commands\CreateWorkspaceApp;
use App\Commands\DeleteWorkspaceApp;
use App\Commands\EditWorkspaceApp;
use App\Commands\GetWorkspaceApp;
use App\Entities\Vulnerability;
use App\Entities\WorkspaceApp;
use App\Transformers\VulnerabilityTransformer;
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
     * Create a new Vulnerability in a Ruggedy WorkspaceApp
     *
     * @POST("/workspace/app/vulnerability/create/{workspaceAppId}", as="workspace.app.create.vulnerability",
     *     where={"workspaceAppId":"[0-9]+"})
     *
     * @param $workspaceAppId
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse
     */
    public function createRuggedyAppVulnerability($workspaceAppId)
    {
        $vulnerability = new Vulnerability();
        $vulnerability->setFromArray($this->request->json('vulnerability'));

        $assetIds = $this->request->json('assetIds');
        $command  = new CreateVulnerability(intval($workspaceAppId), $vulnerability, $assetIds);

        return $this->sendCommandToBusHelper($command, new VulnerabilityTransformer());
    }

    /**
     * Edit a Vulnerability that has been created in a Ruggedy App
     *
     * @PUT("/workspace/app/vulnerability/{vulnerabilityId}", as="workspace.app.edit.vulnerability",
     *     where={"vulnerabilityId":"[0-9]+"})
     *
     * @param $vulnerabilityId
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse
     */
    public function editRuggedyAppVulnerability($vulnerabilityId)
    {
        $command = new EditVulnerability(
            $vulnerabilityId,
            $this->request->json('vulnerability'),
            $this->request->json('assetIds')
        );

        return $this->sendCommandToBusHelper($command, new VulnerabilityTransformer());
    }

    /**
     * Edit a Vulnerability that has been created in a Ruggedy App
     *
     * @DELETE("/workspace/app/vulnerability/{vulnerabilityId}", as="workspace.app.delete.vulnerability",
     *     where={"vulnerabilityId":"[0-9]+"})
     *
     * @param $vulnerabilityId
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse
     */
    public function deleteRuggedyAppVulnerability($vulnerabilityId)
    {
        $command = new DeleteVulnerability(intval($vulnerabilityId), true);
        return $this->sendCommandToBusHelper($command, new VulnerabilityTransformer());
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