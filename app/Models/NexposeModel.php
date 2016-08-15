<?php

namespace App\Models;

use App\Contracts\CollectsPortInformation;
use App\Contracts\CollectsScanOutput;
use Illuminate\Support\Collection;

class NexposeModel extends AbstractXmlModel implements CollectsScanOutput, CollectsPortInformation
{
    /**
     * @inheritdoc
     *
     * @return Collection
     */
    public function getMethodsRequiringAPortId(): Collection
    {
        return new Collection([]);
    }
}