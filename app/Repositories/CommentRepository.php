<?php

namespace App\Repositories;

use App\Entities\Comment;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use LaravelDoctrine\ORM\Pagination\PaginatesFromRequest;

class CommentRepository extends EntityRepository
{
    use PaginatesFromRequest;

    /**
     * Get commments newer than a certain date
     *
     * @param int $vulnerabilityId
     * @param string $newerThan
     * @return array
     */
    public function findNewComments(int $vulnerabilityId, string $newerThan)
    {
        return $this->createQueryBuilder('c')
            ->addCriteria(
                Criteria::create()
                    ->andWhere(Criteria::expr()->eq(Comment::VULNERABILITY_ID, $vulnerabilityId))
                    ->andWhere(Criteria::expr()->gt(Comment::CREATED_AT, $newerThan))
            )->getQuery()
            ->getResult();
    }

    /**
     * Find all the comments related to the given vulnerability ID
     *
     * @param int $vulnerabilityId
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function findByVulnerability(int $vulnerabilityId = 0)
    {
        return $this->paginate(
            $this->addOrdering(
                $this->createQueryBuilder($this->getQueryBuilderAlias())
                     ->addCriteria(
                         Criteria::create()->where(
                             Criteria::expr()->eq('c.vulnerability_id', $vulnerabilityId)
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
        return 'c';
    }

    /**
     * @inheritdoc
     *
     * @return string
     */
    protected function getPageName(): string
    {
        return 'comments_page';
    }

    /**
     * Get the number of records to show on each page
     *
     * @return int
     */
    protected function getPerPage(): int
    {
        return 10;
    }

    /**
     * @inheritdoc
     *
     * @param QueryBuilder $queryBuilder
     * @return QueryBuilder
     */
    protected function addOrdering(QueryBuilder $queryBuilder): QueryBuilder
    {
        return $queryBuilder->orderBy('c.created_at', Criteria::DESC);
    }
}