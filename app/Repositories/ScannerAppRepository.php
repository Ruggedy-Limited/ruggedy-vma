<?php

namespace App\Repositories;

use App\Entities\ScannerApp;
use Doctrine\ORM\EntityRepository;

class ScannerAppRepository extends EntityRepository
{
    /**
     * Find a ScannerApp entity by name
     *
     * @param string $name
     * @return null|object
     */
    public function findByName(string $name)
    {
        return $this->findOneBy([ScannerApp::NAME => $name]);
    }
}