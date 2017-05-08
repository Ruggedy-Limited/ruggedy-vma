<?php

namespace App\Repositories;

use App\Contracts\Searchable;
use App\Entities\Base\AbstractEntity;
use App\Entities\WorkspaceApp;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;
use Illuminate\Support\Collection;
use LaravelDoctrine\ORM\Pagination\PaginatesFromRequest;

class WorkspaceAppRepository extends AbstractSearchableRepository implements Searchable
{
    use PaginatesFromRequest;

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
     * Find all the apps in a workspace
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
                            Criteria::expr()->eq('wa.workspace_id', $workspaceId)
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
     * @return Collection
     */
    public function getSearchableFields(): Collection
    {
        return collect([WorkspaceApp::NAME, WorkspaceApp::DESCRIPTION]);
    }

    /**
     * @inheritdoc
     *
     * @return string
     */
    protected function getQueryBuilderAlias(): string
    {
        return 'wa';
    }

    /**
     * @inheritdoc
     *
     * @return string
     */
    protected function getPageName(): string
    {
        return 'apps_page';
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
            ->orderBy('wa.created_at', Criteria::DESC);
    }
}