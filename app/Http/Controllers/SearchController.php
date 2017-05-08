<?php

namespace App\Http\Controllers;

use App\Commands\Search;

/**
 * @Middleware({"web", "auth"})
 */
class SearchController extends AbstractController
{
    /**
     * Search the database for matching entities
     *
     * @POST("/search", as="search.results")
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function search()
    {
        $searchTerm = $this->request->get('s', null);
        if (empty($searchTerm)) {
            // return view with empty result set.
            return view('search-results', ['searchResults' => [], 'searchTerm' => '']);
        }

        $command       = new Search($searchTerm);
        $searchResults = $this->sendCommandToBusHelper($command);

        return view('search-results', ['searchResults' => $searchResults, 'searchTerm' => $searchTerm]);
    }

    protected function getValidationRules(): array
    {
        return [];
    }

    protected function getValidationMessages(): array
    {
        return [];
    }
}