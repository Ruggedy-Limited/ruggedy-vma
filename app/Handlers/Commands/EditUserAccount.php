<?php

namespace App\Handlers\Commands;

use App\Commands\EditUserAccount as EditUserAccountCommand;
use App\Entities\User;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\InvalidInputException;
use App\Exceptions\UserNotFoundException;
use App\Policies\ComponentPolicy;
use App\Repositories\UserRepository;
use Doctrine\ORM\EntityManager;
use Exception;

class EditUserAccount extends CommandHandler
{
    /** @var UserRepository */
    protected $userRepository;
    
    /** @var EntityManager */
    protected $em;

    /**
     * EditUserAccount constructor.
     *
     * @param UserRepository $userRepository
     * @param EntityManager $em
     */
    public function __construct(UserRepository $userRepository, EntityManager $em)
    {
        $this->userRepository = $userRepository;
        $this->em             = $em;
    }

    /**
     * Process the EditUserAccount command
     *
     * @param EditUserAccountCommand $command
     * @return User
     * @throws ActionNotPermittedException
     * @throws Exception
     */
    public function handle(EditUserAccountCommand $command)
    {
        // Get the authenticated user
        $requestingUser = $this->authenticate();

        $userId = $command->getId();
        // Check that the required member is set on the command
        if (!isset($userId)) {
            throw new InvalidInputException("The required userId member is not set on the command object");
        }

        $user = $requestingUser;
        if ($requestingUser->getId() !== $userId) {
            $user = $this->userRepository->find($userId);
        }

        if (empty($user)) {
            throw new UserNotFoundException("There is not existing User with the given ID");
        }

        // Check that the user is editing their own account
        if ($requestingUser->cannot(ComponentPolicy::ACTION_EDIT, $user)) {
            throw new ActionNotPermittedException("User editing account is not the account owner");
        }

        $profileChanges = $command->getRequestedChanges();

        // Change the password where necessary
        $password = $user->getPassword();
        if (isset($profileChanges[User::PASSWORD])) {
            $password = bcrypt($profileChanges[User::PASSWORD]);
        }
        $profileChanges[User::PASSWORD] = $password;

        // Apply the changes to the User Model
        $user->setFromArray($profileChanges);

        // Save the changes
        $this->em->persist($user);
        $this->em->flush($user);
        $this->em->refresh($user);

        return $user;
    }

    /**
     * @return UserRepository
     */
    public function getUserRepository()
    {
        return $this->userRepository;
    }

    /**
     * @param UserRepository $userRepository
     */
    public function setUserRepository($userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @return EntityManager
     */
    public function getEm()
    {
        return $this->em;
    }

    /**
     * @param EntityManager $em
     */
    public function setEm($em)
    {
        $this->em = $em;
    }
}