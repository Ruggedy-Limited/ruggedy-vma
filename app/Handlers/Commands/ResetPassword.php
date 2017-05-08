<?php

namespace App\Handlers\Commands;

use App\Commands\ResetPassword as ResetPasswordCommand;
use Doctrine\ORM\EntityManager;

class ResetPassword
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function handle(ResetPasswordCommand $command)
    {
        $this->em->persist($command->getUser());
        $this->em->flush($command->getUser());
    }
}