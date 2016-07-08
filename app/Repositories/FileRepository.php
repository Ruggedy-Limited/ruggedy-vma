<?php

namespace App\Repositories;

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
        $result = $this->findBy([
            'deleted'   => false,
            'processed' => false,
        ], [
            'workspace_id' => 'ASC',
        ]);

        if (empty($result)) {
            return new Collection();
        }

        return new Collection($result);
    }
}