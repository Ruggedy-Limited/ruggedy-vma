<?php

namespace App\Handlers\Commands;

use App\Commands\EditUserAccount as EditUserAccountCommand;
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
     * @return stdClass
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
        $this->getEm()->persist($requestingUser);
        $this->getEm()->flush($requestingUser);

        return $requestingUser->toStdClass([
            'name', 'email', 'photo_url', 'uses_two_factor_auth', 'created_at', 'updated_at'
        ]);
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