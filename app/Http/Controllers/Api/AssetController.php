<?php

namespace App\Http\Controllers\Api;

use App\Commands\DeleteAsset;
use App\Commands\EditAsset;
use App\Commands\GetAssetsInProject;
use App\Commands\GetAssetsInWorkspace;
use App\Commands\GetAssetsMasterList;
use App\Commands\UploadScanOutput;
use App\Entities\Asset;
use App\Models\MessagingModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\UploadedFile;

/**
 * @Controller(prefix="api")
 * @Middleware("auth:api")
 */
class AssetController extends AbstractController
{
    /**
     * Upload a scan file for processing
     *
     * @POST("/asset/{workspaceId}", as="asset.import", where={"workspaceId":"[0-9]+"})
     *
     * @param $workspaceId
     * @return ResponseFactory|JsonResponse
     */
    public function uploadScanOutput($workspaceId)
    {
        $file = $this->getRequest()->file('file');
        if (!isset($file) || !($file instanceof UploadedFile)) {
            $file = new UploadedFile('/dev/null', 'null');
        }

        $command = new UploadScanOutput(
            intval($workspaceId),
            $this->getRequest()->json()->all(),
            $file
        );

        return $this->sendCommandToBusHelper($command);
    }

    /**
     * Edit the details of an existing Asset
     *
     * @PUT("/asset/{assetId}", as="asset.edit", where={"assetId":"[0-9]+"})
     *
     * @param $assetId
     * @return ResponseFactory|JsonResponse
     */
    public function editAsset($assetId)
    {
        $command = new EditAsset(intval($assetId), $this->getRequest()->json()->all());
        return $this->sendCommandToBusHelper($command);
    }

    /**
     * Delete and Asset
     *
     * @DELETE("/asset/{assetId}/{confirm?}", as="asset.delete", where={"assetId":"[0-9]+", "confirm":"^confirm$"})
     *
     * @param $assetId
     * @param null $confirm
     * @return ResponseFactory|JsonResponse
     */
    public function deleteAsset($assetId, $confirm = null)
    {
        $command = new DeleteAsset(intval($assetId), boolval($confirm));
        return $this->sendCommandToBusHelper($command);
    }

    /**
     * Get all the Assets that are owned by the authenticated User
     * 
     * @GET("/assets", as="assets.master.list")
     * 
     * @return ResponseFactory|JsonResponse
     */
    public function assetsMasterList()
    {
        $command = new GetAssetsMasterList(0);
        return $this->sendCommandToBusHelper($command);
    }

    /**
     * Get all the Assets that belong to a particular Project
     *
     * @GET("/assets/project/{projectId}", as="assets.project.list", where={"projectId":"[0-9]+"})
     *
     * @param $projectId
     * @return ResponseFactory|JsonResponse
     */
    public function assetByProjectList($projectId)
    {
        $command = new GetAssetsInProject(intval($projectId));
        return $this->sendCommandToBusHelper($command);
    }

    /**
     * Get all the Assets that belong to a particular Workspace
     *
     * @GET("/assets/workspace/{workspaceId}", as="assets.workspace.list", where={"workspaceId":"[0-9]+"})
     *
     * @param $workspaceId
     * @return ResponseFactory|JsonResponse
     */
    public function assetsByWorkspace($workspaceId)
    {
        $command = new GetAssetsInWorkspace($workspaceId);
        return $this->sendCommandToBusHelper($command);
    }

    /**
     * @inheritdoc
     */
    protected function getValidationRules(): array
    {
        return [
            'name'       => 'bail|filled|alpha_num',
            'cpe'        => [
                'bail',
                'filled',
                'regex:' . Asset::REGEX_CPE,
            ],
            'vendorName' => 'bail|filled|alpha_num',
            'ipV4'       => 'bail|filled|ip',
            'ipV6'       => 'bail|filled|ip',
            'hostname'   => 'bail|filled|url',
            'macAddress' => [
                'bail',
                'filled',
                'regex:' . Asset::REGEX_MAC_ADDRESS,
            ],
            'osVersion'  => 'bail|filled|alpha_num',
            'deleted'    => 'bail|filled|boolean',
            'suppressed' => 'bail|filled|boolean',
        ];
    }
}