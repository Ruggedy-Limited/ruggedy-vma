<?php
​
namespace App\Transformers;
​
use App\Entities\User;
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
            'id'                   => $invoice->getId(),
            'name'                 => $invoice->getName(),
            'emailAddress'         => $invoice->getEmail(),
            'photo'                => $invoice->getPhotoUrl(),
            'twoFactorAuthEnabled' => $invoice->getUsesTwoFactorAuth(),
            'createdDate'          => $invoice->getCreatedAt(),
            'modifiedDate'         => $invoice->getUpdatedAt(),
        ]
    }
}