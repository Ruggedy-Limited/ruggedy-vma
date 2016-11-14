<?php

namespace App\Handlers\Commands;

use App\Commands\EditUserAccount as EditUserAccountCommand;
use App\Entities\User;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\InvalidInputException;
use App\Repositories\UserRepository;
use Doctrine\ORM\EntityManager;
use Exception;
use stdClass;


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

        // Check that the user is editing their own account
        if ($requestingUser->getId() !== intval($userId)) {
            throw new ActionNotPermittedException("User editing account is not the account owner");
        }

        // Apply the changes to the User Model
        $profileChanges = $command->getRequestedChanges();
        $requestingUser->setFromArray($profileChanges);

        // Save the changes
        $this->em->persist($requestingUser);
        $this->em->flush($requestingUser);

        return $requestingUser;
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