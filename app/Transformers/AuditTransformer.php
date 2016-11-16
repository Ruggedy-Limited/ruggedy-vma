<?php

namespace App\Transformers;

use App\Entities\Audit;
use League\Fractal\TransformerAbstract;

class AuditTransformer extends TransformerAbstract
{
    /**
     * Transform an Audit entity
     *
     * @param Audit $audit
     * @return array
     */
    public function transform(Audit $audit)
    {
        return [
            'id'                  => $audit->getId(),
            'description'         => $audit->getDescription(),
            'result'              => $audit->getResult(),
            'solution'            => $audit->getSolution(),
            'policyValue'         => $audit->getPolicyValue(),
            'actualValue'         => $audit->getActualValue(),
            'complianceCheckId'   => $audit->getComplianceCheckId(),
            'complianceCheckName' => $audit->getComplianceCheckName(),
            'auditFile'           => $audit->getAuditFile(),
            'reference'           => $audit->getReference(),
            'agent'               => $audit->getAgent(),
            'info'                => $audit->getInfo(),
            'output'              => $audit->getOutput(),
            'uname'               => $audit->getUname(),
            'seeAlso'             => $audit->getSeeAlso(),
            'createdDate'         => $audit->getCreatedAt()->format(env('APP_DATE_FORMAT')),
            'modifiedDate'        => $audit->getUpdatedAt()->format(env('APP_DATE_FORMAT')),
        ];
    }
}