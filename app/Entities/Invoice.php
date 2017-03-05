<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entities\Invoice
 *
 * @ORM\Entity(repositoryClass="App\Repositories\InvoiceRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Invoice extends Base\Invoice
{
}