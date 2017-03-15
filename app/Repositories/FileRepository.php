<?php

namespace App\Repositories;

use App\Entities\Asset;
use App\Entities\Base\AbstractEntity;
use App\Entities\File;
use App\Entities\Workspace;
use App\Entities\WorkspaceApp;
use Doctrine\ORM\EntityRepository;
use Illuminate\Support\Collection;

class FileRepository extends EntityRepository
{
    /**
     * Find all unprocessed files ordered by workspace_id
     *
     * @return Collection
     */
    public function findUnprocessed(): Collection
    {
        $queryBuilder = $this->_em
            ->createQueryBuilder()
            ->select('f', 'wa', 'w', 's')
            ->from(File::class, 'f')
            ->leftJoin('f.' . File::WORKSPACEAPP, 'wa')
            ->leftJoin('wa.' . WorkspaceApp::WORKSPACE, 'w')
            ->leftJoin('wa.' . WorkspaceApp::SCANNERAPP, 's')
            ->where('f.' . File::DELETED . ' = :deleted AND f.' . File::PROCESSED . ' = :processed')
            ->setParameter('deleted', false)
            ->setParameter('processed', false)
            ->orderBy('w.' . Workspace::ID, 'ASC');

        $result = $queryBuilder->getQuery()->getResult();
        if (empty($result)) {
            return new Collection();
        }

        return new Collection($result);
    }

    /**
     * Find a WorkspaceApp and fetch any associated Files, Vulnerabilities and Assets
     *
     * @param int $id
     * @return AbstractEntity
     */
    public function findOneForFileView(int $id)
    {
        return $this->_em->createQueryBuilder()
            ->select('f', 'a', 'av', 'fv')
            ->from(File::class, 'f')
            ->leftJoin('f.' . File::ASSETS, 'a')
            ->leftJoin('a.' . Asset::VULNERABILITIES, 'av')
            ->leftJoin('f.' . File::VULNERABILITIES, 'fv')
            ->where('f.' . File::ID . ' = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}