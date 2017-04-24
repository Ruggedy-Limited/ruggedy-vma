<?php

namespace App\Handlers\Commands;

use App\Commands\DeleteUser as DeleteUserCommand;
use App\Entities\User;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\UserNotFoundException;
use App\Policies\ComponentPolicy;
use App\Repositories\UserRepository;
use Doctrine\ORM\EntityManager;

class DeleteUser extends CommandHandler
{
    /** @var EntityManager */
    protected $em;

    /** @var UserRepository */
    protected $repository;

    /**
     * DeleteUser constructor.
     *
     * @param UserRepository $userRepository
     * @param EntityManager $em
     */
    public function __construct(UserRepository $userRepository, EntityManager $em)
    {
        $this->em         = $em;
        $this->repository = $userRepository;
    }

    /**
     * Process the DeleteUser command.
     *
     * @param DeleteUserCommand $command
     * @return User
     * @throws ActionNotPermittedException
     * @throws UserNotFoundException
     */
    public function handle(DeleteUserCommand $command)
    {
        $requestingUser = $this->authenticate();

        /** @var User $user */
        $user = $this->repository->find($command->getId());
        if (empty($user)) {
            throw new UserNotFoundException("An existing User with the given ID was not found.");
        }

        if ($requestingUser->cannot(ComponentPolicy::ACTION_DELETE, $user)) {
            throw new ActionNotPermittedException(
                "The authenticated User does not have permission to delete other Users."
            );
        }

        $this->em->remove($user);
        $this->em->flush($user);

        return $user;
    }
}