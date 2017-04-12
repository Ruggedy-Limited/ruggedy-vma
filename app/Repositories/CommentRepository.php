<?php

namespace App\Repositories;

use App\Entities\Comment;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;

class CommentRepository extends EntityRepository
{
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
}