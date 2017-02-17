<?php

namespace App\Transformers;

use App\Entities\WorkspaceApp;
use League\Fractal\TransformerAbstract;

class WorkspaceAppTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'files',
        'scannerApp',
        'workspace',
    ];

     /**
     * Transform a WorkspaceApp entity for the API
     *
     * @param WorkspaceApp $workspaceApp
     * @return array
     */
    public function transform(WorkspaceApp $workspaceApp)
    {
        return [
            'id'           => $workspaceApp->getId(),
            'name'         => $workspaceApp->getName(),
            'description'  => $workspaceApp->getDescription(),
            'ownerId'      => $workspaceApp->getUser()->getId(),
            'createdDate'  => $workspaceApp->getCreatedAt()->format(env('APP_DATE_FORMAT')),
            'modifiedDate' => $workspaceApp->getUpdatedAt()->format(env('APP_DATE_FORMAT')),
        ];
    }

    /**
     * Optional include for a list of Files that belong to the WorkspaceApp
     *
     * @param WorkspaceApp $workspaceApp
     * @return \League\Fractal\Resource\Collection
     */
    public function includeFiles(WorkspaceApp $workspaceApp)
    {
        return $this->collection($workspaceApp->getFiles(), new FileTransformer());
    }

    /**
     * Optional include for Vulnerabilities
     *
     * @param WorkspaceApp $workspaceApp
     * @return \League\Fractal\Resource\Item
     */
    public function includeScannerApp(WorkspaceApp $workspaceApp)
    {
        return $this->item($workspaceApp->getScannerApp(), new ScannerAppTransformer());
    }

    /**
     * Optional include for Workspace
     *
     * @param WorkspaceApp $workspaceApp
     * @return \League\Fractal\Resource\Item
     */
    public function includeWorkspace(WorkspaceApp $workspaceApp)
    {
        return $this->item($workspaceApp->getWorkspace(), new WorkspaceTransformer());
    }
}