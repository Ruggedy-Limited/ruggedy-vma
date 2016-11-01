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
            'id'                   => $file->getId(),
            'filename'             => basename($file->getPath()),
            'format'               => $file->getFormat(),
            'size'                 => $file->getSize(),
            'scanner'              => $file->getScannerApp(),
            'workspace'            => $file->getWorkspace(),
            'user'                 => $file->getUser(),
            'isProcessed'          => $file->getProcessed(),
            'isDeleted'            => $file->getDeleted(),
            'createdDate'          => $file->getCreatedAt(),
            'modifiedDate'         => $file->getUpdatedAt(),
        ];
    }
}