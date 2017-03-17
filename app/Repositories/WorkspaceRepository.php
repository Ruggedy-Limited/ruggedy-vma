<?php

namespace App\Repositories;

use App\Contracts\Searchable;
use App\Entities\Workspace;
use Illuminate\Support\Collection;

class WorkspaceRepository extends AbstractSearchableRepository implements Searchable
{
    /**
     * @inheritdoc
     *
     * @return Collection
     */
    protected function getSearchableFields(): Collection
    {
        return collect([Workspace::NAME, Workspace::DESCRIPTION]);
    }
}