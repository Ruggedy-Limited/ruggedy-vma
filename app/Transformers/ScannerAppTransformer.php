<?php

namespace App\Transformers;

use App\Entities\ScannerApp;
use League\Fractal\TransformerAbstract;

class ScannerAppTransformer extends TransformerAbstract
{
    /**
     * Transform a ScannerApp entity for the API
     *
     * @param ScannerApp $scannerApp
     * @return array
     */
    public function transform(ScannerApp $scannerApp)
    {
        return [
            'id'           => $scannerApp->getId(),
            'name'         => $scannerApp->getName(),
            'description'  => $scannerApp->getDescription(),
            'files'        => $scannerApp->getFiles(),
            'createdDate'  => $scannerApp->getCreatedAt(),
            'modifiedDate' => $scannerApp->getUpdatedAt(),
        ];
    }
}