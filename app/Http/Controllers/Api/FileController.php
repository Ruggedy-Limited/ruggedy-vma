<?php

namespace App\Http\Controllers\Api;

use App\Commands\GetFile;
use App\Transformers\FileTransformer;

/**
 * @Controller(prefix="api")
 * @Middleware("auth:api")
 */
class FileController extends AbstractController
{
    /**
     * Get a single Workspace and various related data by using optional Fractal Transformer includes
     *
     * @GET("/file/{fileId}", as="file.get", where={"fileId":"[0-9]+"})
     *
     * @param $fileId
     * @return \Illuminate\Contracts\Routing\ResponseFactory
     */
    public function getFile($fileId)
    {
        $command = new GetFile(intval($fileId));
        return $this->sendCommandToBusHelper($command, new FileTransformer());
    }

    /**
     * @inheritdoc
     */
    protected function getValidationRules(): array
    {
        return [];
    }
}