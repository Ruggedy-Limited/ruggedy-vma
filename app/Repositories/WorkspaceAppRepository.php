<?php

namespace App\Repositories;

use App\Contracts\Searchable;
use App\Entities\Base\AbstractEntity;
use App\Entities\File;
use App\Entities\Vulnerability;
use App\Entities\WorkspaceApp;
use Illuminate\Support\Collection;

class WorkspaceAppRepository extends AbstractSearchableRepository implements Searchable
{
    /**
     * Find a WorkspaceApp and fetch any associated Files, Vulnerabilities and Assets
     *
     * @param int $id
     * @return AbstractEntity
     */
    public function findOneForWorkspaceAppView(int $id)
    {
        return $this->_em->createQueryBuilder()
             ->select('wa', 'f', 's')
             ->from(WorkspaceApp::class, 'wa')
             ->leftJoin('wa.' . WorkspaceApp::FILES, 'f')
             ->leftJoin('wa.' . WorkspaceApp::SCANNERAPP, 's')
             ->where('wa.' . WorkspaceApp::ID . ' = :id')
             ->setParameter('id', $id)
             ->getQuery()
             ->getOneOrNullResult();
    }

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