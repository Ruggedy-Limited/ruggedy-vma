<?php

namespace App\Repositories;

use App\Entities\File;
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
            File::DELETED   => false,
            File::PROCESSED => false,
        ], [
            File::WORKSPACE_ID => 'ASC',
        ]);

        if (empty($result)) {
            return new Collection();
        }

        return new Collection($result);
    }
}