<?php

namespace App\Contracts;

use Illuminate\Support\Collection;

interface CollectsPortInformation
{
    /**
     * Get a Collection of setter methods that require an additional port ID parameter when called
     *
     * @return Collection
     */
    function getMethodsRequiringAPortId(): Collection;
}