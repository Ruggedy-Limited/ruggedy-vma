<?php

namespace App\Repositories;

use App\Entities\JiraIssue;
use Carbon\Carbon;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;

class JiraIssueRepository extends EntityRepository
{
    /**
     * Get all Jira Issues where the status has not yet been updated since the last update cycle
     *
     * @param Carbon $time
     * @return \Illuminate\Support\Collection
     */
    public function findStatusesToSync(Carbon $time)
    {
        return collect(
            $this->createQueryBuilder('j')
                ->where(
                    Criteria::create()->where(
                        Criteria::expr()->lte(
                            JiraIssue::UPDATED_AT,
                            $time->format(env('APP_DATE_FORMAT'))
                        )
                    )
                )
                ->getQuery()
                ->getResult()
        );
    }
}