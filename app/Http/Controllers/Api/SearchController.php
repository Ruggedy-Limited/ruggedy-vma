<?php

namespace App\Http\Controllers\Api;

use App\Commands\Search;
use App\Repositories\AbstractSearchableRepository;

/**
 * @Controller(prefix="api")
 * @Middleware("auth:api")
 */
class SearchController extends AbstractController
{
    /**
     * Get a single Folder and various related data by using optional Fractal Transformer includes
     *
     * @POST("/search/{searchType}", as="search", where={"searchType":"[1-4]{1}"})
     *
     * @param $type
     * @return \Illuminate\Contracts\Routing\ResponseFactory
     */
    public function search($type)
    {
        $command = new Search($this->request->get('q'), intval($type));
        $transformerClass = AbstractSearchableRepository::getTransformerForSearchType($type);
        return $this->sendCommandToBusHelper($command, new $transformerClass());
    }

    /**
     * @inheritdoc
     * @return array
     */
    protected function getValidationRules(): array
    {
        return [
            'q'    => 'bail|required',
            'type' => 'bail|int|in:1,2,3,4',
        ];
    }

}