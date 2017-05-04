<?php

namespace App\Repositories;

use App\Entities\Asset;
use App\Entities\Base\AbstractEntity;
use App\Entities\File;
use App\Entities\Workspace;
use App\Entities\WorkspaceApp;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Illuminate\Support\Collection;
use LaravelDoctrine\ORM\Pagination\PaginatesFromRequest;

class FileRepository extends EntityRepository
{
    use PaginatesFromRequest;

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

    /**
     * Find all the files for this App
     *
     * @param int $appId
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function findByApp(int $appId = 0)
    {
        return $this->paginate(
            $this->addOrdering(
                $this->createQueryBuilder($this->getQueryBuilderAlias())
                     ->addCriteria(
                         Criteria::create()->where(
                             Criteria::expr()->eq('fi.workspace_app_id', $appId)
                         )
                     )
            )->getQuery(),
            $this->getPerPage(),
            $this->getPageName(),
            false
        )->setPageName($this->getPageName());
    }

    /**
     * @inheritdoc
     *
     * @return string
     */
    protected function getQueryBuilderAlias(): string
    {
        return 'fi';
    }

    /**
     * @inheritdoc
     *
     * @return string
     */
    protected function getPageName(): string
    {
        return 'files_page';
    }

    /**
     * @inheritdoc
     *
     * @return int
     */
    protected function getPerPage(): int
    {
        return 12;
    }

    /**
     * @inheritdoc
     *
     * @param QueryBuilder $queryBuilder
     * @return QueryBuilder
     */
    protected function addOrdering(QueryBuilder $queryBuilder): QueryBuilder
    {
        return $queryBuilder->orderBy('fi.created_at', Criteria::DESC);
    }
}