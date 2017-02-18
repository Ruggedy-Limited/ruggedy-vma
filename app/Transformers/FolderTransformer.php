<?php

namespace App\Transformers;

use App\Entities\Folder;
use League\Fractal\TransformerAbstract;

class FolderTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'vulnerabilities',
        'workspace',
    ];

     /**
     * Transform a Folder entity for the API
     *
     * @param Folder $folder
     * @return array
     */
    public function transform(Folder $folder)
    {
        return [
            'id'           => $folder->getId(),
            'name'         => $folder->getName(),
            'description'  => $folder->getDescription(),
            'ownerId'      => $folder->getUser()->getId(),
            'createdDate'  => $folder->getCreatedAt()->format(env('APP_DATE_FORMAT')),
            'modifiedDate' => $folder->getUpdatedAt()->format(env('APP_DATE_FORMAT')),
        ];
    }

    /**
     * Optional include for Vulnerabilities
     *
     * @param Folder $folder
     * @return \League\Fractal\Resource\Collection
     */
    public function includeVulnerabilities(Folder $folder)
    {
        return $this->collection($folder->getVulnerabilities(), new VulnerabilityTransformer());
    }

    /**
     * Optional include for Workspace
     *
     * @param Folder $folder
     * @return \League\Fractal\Resource\Item
     */
    public function includeWorkspace(Folder $folder)
    {
        return $this->item($folder->getWorkspace(), new WorkspaceTransformer());
    }
}