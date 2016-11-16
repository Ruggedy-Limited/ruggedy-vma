<?php

namespace App\Transformers;

use App\Entities\Invoice;
use League\Fractal\TransformerAbstract;

class InvoiceTransformer extends TransformerAbstract
{
    /**
     * Transform a Invoice entity for the API
     *
     * @param Invoice $invoice
     * @return array
     */
    public function transform(Invoice $invoice)
    {
        return [
            'id'           => $invoice->getId(),
            'user'         => $invoice->getUser(),
            'team'         => $invoice->getTeam(),
            'providerId'   => $invoice->getProviderId(),
            'total'        => $invoice->getTotal(),
            'taxAmount'    => $invoice->getTax(),
            'createdDate'  => $invoice->getCreatedAt()->format(env('APP_DATE_FORMAT')),
            'modifiedDate' => $invoice->getUpdatedAt()->format(env('APP_DATE_FORMAT')),
        ];
    }
}