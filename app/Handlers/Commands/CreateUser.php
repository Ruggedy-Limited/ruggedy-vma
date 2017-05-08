<?php

namespace App\Handlers\Commands;

use App\Commands\CreateUser as CreateUserCommand;
use App\Entities\User;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\InvalidInputException;
use App\Repositories\UserRepository;
use Doctrine\ORM\EntityManager;

class CreateUser extends CommandHandler
{
    /** @var UserRepository */
    protected $repository;

    /** @var EntityManager */
    protected $em;

    /**
     * CreateUser constructor.
     *
     * @param UserRepository $repository
     * @param EntityManager $em
     */
    public function __construct(UserRepository $repository, EntityManager $em)
    {
        $this->repository = $repository;
        $this->em         = $em;
    }

    /**
     * Process the CreateUser command.
     *
     * @param CreateUserCommand $command
     * @return User
     * @throws ActionNotPermittedException
     * @throws InvalidInputException
     */
    public function handle(CreateUserCommand $command)
    {
        $requestingUser = $this->authenticate();

        /** @var User $user */
        $user = $command->getEntity();
        if (empty($user) || empty($user->getName()) || empty($user->getEmail())) {
            throw new InvalidInputException("One or more required fields are empty");
        }

        // Make sure the authenticated User is an admin. Only admins can create User accounts
        if (!$requestingUser->isAdmin()) {
            throw new ActionNotPermittedException("Only admins can create Users.");
        }

        $user->setDeleted(false);

        $this->em->persist($user);
        $this->em->flush($user);
        $this->em->refresh($user);

        return $user;
    }
}