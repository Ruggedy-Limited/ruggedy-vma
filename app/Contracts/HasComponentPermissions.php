<?php

namespace App\Contracts;

use Doctrine\Common\Collections\Collection;


interface HasComponentPermissions
{
    /**
     * Get the permissions related to this User or Team
     *
     * @return Collection
     */
    public function getPermissions(): Collection;
}