<?php

namespace App\Repositories;

use App\Contracts\Searchable;
use App\Entities\Base\AbstractEntity;
use App\Entities\ScannerApp;
use App\Entities\Workspace;
use App\Entities\WorkspaceApp;
use Illuminate\Support\Collection;

class WorkspaceRepository extends AbstractSearchableRepository implements Searchable
{
    /**
     * Find a Workspace and fetch any associated WorkspaceApps, ScannerApps and Folders
     *
     * @param int $id
     * @return AbstractEntity
     */
    public function findOneForWorkspaceView(int $id)
    {
        return $this->_em->createQueryBuilder()
            ->select('w', 'wa', 'f', 's')
            ->from(Workspace::class, 'w')
            ->leftJoin('w.' . Workspace::WORKSPACEAPPS, 'wa')
            ->leftJoin('w.' . Workspace::FOLDERS, 'f')
            ->leftJoin('wa.' . WorkspaceApp::SCANNERAPP, 's')
            ->where('w.' . Workspace::ID . ' = :id')
            ->setParameter('id', $id)
            ->orderBy('wa.' . ScannerApp::NAME, 'ASC')
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @inheritdoc
     *
     * @return Collection
     */
    public function getSearchableFields(): Collection
    {
        return collect([Workspace::NAME, Workspace::DESCRIPTION]);
    }
}