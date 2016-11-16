<?php

namespace App\Transformers;

use App\Entities\Asset;
use App\Entities\Vulnerability;
use App\Entities\Workspace;
use League\Fractal\Manager;
use League\Fractal\TransformerAbstract;

class WorkspaceTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
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
        return $this->collection($workspace->getFiles(), new FileTransformer());
    }
}