<?php

namespace App\Repositories;

use App\Contracts\Searchable;
use App\Entities\Base\AbstractEntity;
use App\Entities\ScannerApp;
use App\Entities\Workspace;
use App\Entities\WorkspaceApp;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;
use Illuminate\Support\Collection;
use LaravelDoctrine\ORM\Pagination\PaginatesFromRequest;

class WorkspaceRepository extends AbstractSearchableRepository implements Searchable
{
    use PaginatesFromRequest;
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
     * Find all Workspaces, optionally filtering by user ID
     *
     * @param int $userId
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function findAllQuery(int $userId = 0)
    {
        // Create the query builder with the initial criteria
        $queryBuilder = $this->createQueryBuilder($this->getQueryBuilderAlias())
            ->addCriteria(
                Criteria::create()->where(
                    Criteria::expr()->eq('w.deleted', false)
            )
        );

        // Add the user_id filter if necessary
        if (!empty($userId)) {
            $queryBuilder->addCriteria(
                Criteria::create()->andWhere(
                    Criteria::expr()->eq('w.user_id', $userId)
                )
            );
        }

        // Add ordering, paginate and return
        return $this->paginate(
            $this->addOrdering($queryBuilder)->getQuery(),
            $this->getPerPage(),
            $this->getPageName(),
            false
        )->setPageName($this->getPageName());
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

    /**
     * @inheritdoc
     *
     * @return string
     */
    protected function getQueryBuilderAlias(): string
    {
        return 'w';
    }

    /**
     * @inheritdoc
     *
     * @return string
     */
    protected function getPageName(): string
    {
        return 'workspaces_page';
    }

    /**
     * @inheritdoc
     *
     * @return int
     */
    protected function getPerPage(): int
    {
        return 6;
    }

    /**
     * @inheritdoc
     *
     * @param QueryBuilder $queryBuilder
     * @return QueryBuilder
     */
    protected function addOrdering(QueryBuilder $queryBuilder): QueryBuilder
    {
        return parent::addOrdering($queryBuilder)
                     ->orderBy('w.created_at', Criteria::DESC);
    }
}