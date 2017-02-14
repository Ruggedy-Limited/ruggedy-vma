<?php

namespace App\Transformers;

use App\Entities\Workspace;
use App\Entities\WorkspaceApp;
use League\Fractal\TransformerAbstract;

class WorkspaceTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'apps',
        'assets',
        'files',
    ];

     /**
     * Transform a Workspace entity for the API
     *
     * @param Workspace $workspace
     * @return array
     */
    public function transform(Workspace $workspace)
    {
        return [
            'id'           => $workspace->getId(),
            'name'         => $workspace->getName(),
            'ownerId'      => $workspace->getUser()->getId(),
            'isDeleted'    => $workspace->getDeleted(),
            'createdDate'  => $workspace->getCreatedAt()->format(env('APP_DATE_FORMAT')),
            'modifiedDate' => $workspace->getUpdatedAt()->format(env('APP_DATE_FORMAT')),
        ];
    }

    /**
     * Optional include for a unique collection of Apps
     *
     * @param Workspace $workspace
     * @return \League\Fractal\Resource\Collection
     */
    public function includeApps(Workspace $workspace)
    {
        $apps = collect(
            $workspace->getWorkspaceApps()->map(function ($workspaceApp) {
                /** @var WorkspaceApp $workspaceApp */
                return $workspaceApp->getScannerApp();
            })->toArray()
        )->unique();

        return $this->collection($apps, new ScannerAppTransformer());
    }

    /**
     * Optional include for Assets
     *
     * @param Workspace $workspace
     * @return \League\Fractal\Resource\Collection
     */
    public function includeAssets(Workspace $workspace)
    {
        return $this->collection($workspace->getAssets(), new AssetTransformer());
    }

    /**
     * Optional include for Files
     *
     * @param Workspace $workspace
     * @return \League\Fractal\Resource\Collection
     */
    public function includeFiles(Workspace $workspace)
    {
        $files = collect($workspace->getWorkspaceApps()->toArray())->flatMap(function ($workspaceApps) {
            /** @var WorkspaceApp $workspaceApps */
            return collect($workspaceApps->getFiles()->toArray());
        });

        return $this->collection($files, new FileTransformer());
    }
}