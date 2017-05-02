<?php

namespace App\Repositories;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use LaravelDoctrine\ORM\Pagination\PaginatesFromRequest;

class FolderRepository extends EntityRepository
{
    use PaginatesFromRequest;

    /**
     * Find all the folders in a workspace
     *
     * @param int $workspaceId
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function findByWorkspace(int $workspaceId)
    {
        return $this->paginate(
            $this->addOrdering(
                $this->createQueryBuilder($this->getQueryBuilderAlias())
                     ->addCriteria(
                         Criteria::create()->where(
                             Criteria::expr()->eq('f.workspace_id', $workspaceId)
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
        return 'f';
    }

    /**
     * @inheritdoc
     *
     * @return string
     */
    protected function getPageName(): string
    {
        return 'folders_page';
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
        return $queryBuilder->orderBy('f.created_at', Criteria::DESC);
    }
}