<?php

namespace App\Handlers\Commands;

use App\Commands\CommitCurrentUnitOfWork as CommitCurrentUnitOfWorkCommand;
use Doctrine\ORM\EntityManager;

class CommitCurrentUnitOfWork extends CommandHandler
{
    /** @var EntityManager */
    protected $em;

    /**
     * CreateAsset constructor.
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Persist all the entities in the current Doctrine UnitOfWork
     *
     * @param CommitCurrentUnitOfWorkCommand $command
     */
    public function handle(CommitCurrentUnitOfWorkCommand $command)
    {
        $this->em->flush();
    }

    /**
     * @return EntityManager
     */
    public function getEm(): EntityManager
    {
        return $this->em;
    }
}