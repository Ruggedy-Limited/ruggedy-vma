<?php

namespace App\Transformers;

use App\Entities\File;
use League\Fractal\TransformerAbstract;

class FileTransformer extends TransformerAbstract
{
     /**
     * Transform a File entity for the API
     *
     * @param File $file
     * @return array
     */
    public function transform(File $file)
    {
        return [
            'id'           => $file->getId(),
            'filename'     => basename($file->getPath()),
            'format'       => $file->getFormat(),
            'size'         => $file->getSize(),
            'scannerId'    => $file->getScannerAppId(),
            'workspaceId'  => $file->getWorkspace()->getId(),
            'userId'       => $file->getUser()->getId(),
            'isProcessed'  => $file->getProcessed(),
            'isDeleted'    => $file->getDeleted(),
            'createdDate'  => $file->getCreatedAt(),
            'modifiedDate' => $file->getUpdatedAt(),
        ];
    }
}