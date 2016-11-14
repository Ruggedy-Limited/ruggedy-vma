<?php

namespace App\Transformers;

use App\Entities\Workspace;
use League\Fractal\TransformerAbstract;

class WorkspaceTransformer extends TransformerAbstract
{
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
            'assets'       => $workspace->getAssets(),
            'files'        => $workspace->getFiles(),
            'isDeleted'    => $workspace->getDeleted(),
            'createdDate'  => $workspace->getCreatedAt(),
            'modifiedDate' => $workspace->getUpdatedAt(),
        ];
    }
}