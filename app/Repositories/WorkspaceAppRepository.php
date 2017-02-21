<?php

namespace App\Repositories;

use App\Contracts\Searchable;
use App\Entities\WorkspaceApp;
use Illuminate\Support\Collection;

class WorkspaceAppRepository extends AbstractSearchableRepository implements Searchable
{
    /**
     * @inheritdoc
     *
     * @return Collection
     */
    protected function getSearchableFields(): Collection
    {
        return collect([WorkspaceApp::NAME, WorkspaceApp::DESCRIPTION]);
    }
}