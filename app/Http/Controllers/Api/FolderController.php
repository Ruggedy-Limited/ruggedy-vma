<?php

namespace App\Http\Controllers\Api;

use App\Commands\AddRemoveVulnerabilityToFromFolder;
use App\Commands\CreateFolder;
use App\Commands\DeleteFolder;
use App\Commands\EditFolder;
use App\Commands\GetFolder;
use App\Entities\Folder;
use App\Transformers\FolderTransformer;

/**
 * @Controller(prefix="api")
 * @Middleware("auth:api")
 */
class FolderController extends AbstractController
{
    /**
     * Get a single Folder and various related data by using optional Fractal Transformer includes
     *
     * @GET("/folder/{folderId}", as="folder.get", where={"folderId":"[0-9]+"})
     *
     * @param $folderId
     * @return \Illuminate\Contracts\Routing\ResponseFactory
     */
    public function getFolder($folderId)
    {
        $command = new GetFolder(intval($folderId));
        return $this->sendCommandToBusHelper($command, new FolderTransformer());
    }

    /**
     * Create a new Folder
     *
     * @POST("/folder/create/{workspaceId}", as="folder.create", where={"workspaceId":"[0-9]+"})
     *
     * @param $workspaceId
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse
     */
    public function createFolder($workspaceId)
    {
        $folder = new Folder();
        $folder->setFromArray($this->request->json()->all());
        $command = new CreateFolder(intval($workspaceId), $folder);
        return $this->sendCommandToBusHelper($command, new FolderTransformer());
    }

    /**
     * Edit an existing Folder
     *
     * @PUT("/folder/{folderId}", as="folder.edit", where={"folderId":"[0-9]+"})
     *
     * @param $folderId
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse
     */
    public function editFolder($folderId)
    {
        $command = new EditFolder(intval($folderId), $this->request->json()->all());
        return $this->sendCommandToBusHelper($command, new FolderTransformer());
    }

    /**
     * Delete an existing Folder
     *
     * @DELETE("/folder/{folderId}", as="folder.delete", where={"folderId":"[0-9]+"})
     *
     * @param $folderId
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse
     */
    public function deleteFolder($folderId)
    {
        $command = new DeleteFolder(intval($folderId), true);
        return $this->sendCommandToBusHelper($command, new FolderTransformer());
    }

    /**
     * Add a Vulnerability to a Folder
     *
     * @PUT("/folder/{folderId}/{vulnerabilityId}", as="folder.add.vulnerability", where={"folderId":"[0-9]+", "vulnerabilityId":"[0-9]+"})
     *
     * @param $folderId
     * @param $vulnerabilityId
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse
     */
    public function addVulnerabilityToFolder($folderId, $vulnerabilityId)
    {
        $command = new AddRemoveVulnerabilityToFromFolder(intval($folderId), intval($vulnerabilityId));
        return $this->sendCommandToBusHelper($command, new FolderTransformer());
    }

    /**
     * Remove a Vulnerability from a Folder
     *
     * @DELETE("/folder/{folderId}/{vulnerabilityId}", as="folder.remove.vulnerability", where={"folderId":"[0-9]+", "vulnerabilityId":"[0-9]+"})
     *
     * @param $folderId
     * @param $vulnerabilityId
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse
     */
    public function removeVulnerabilityFromFolder($folderId, $vulnerabilityId)
    {
        $command = new AddRemoveVulnerabilityToFromFolder(intval($folderId), intval($vulnerabilityId), true);
        return $this->sendCommandToBusHelper($command, new FolderTransformer());
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