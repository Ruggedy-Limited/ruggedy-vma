<?php

namespace App\Handlers\Commands;

use App\Commands\GetUserInformation as GetUserInformationCommand;
use App\Entities\User;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\InvalidInputException;
use App\Exceptions\UserNotFoundException;
use App\Policies\ComponentPolicy;
use App\Repositories\UserRepository;
use Doctrine\ORM\EntityManager;
use Exception;

class GetUserInformation extends CommandHandler
{
    /** @var UserRepository  */
    protected $userRepository;
    
    /** @var EntityManager  */
    protected $em;

    /**
     * GetUserInformation constructor.
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
     * Process the GetUserInformation command
     *
     * @param GetUserInformationCommand $command
     * @return User
     * @throws ActionNotPermittedException
     * @throws Exception
     * @throws InvalidInputException
     * @throws UserNotFoundException
     */
    public function handle(GetUserInformationCommand $command)
    {
        // Get the authenticated user
        $requestingUser = $this->authenticate();

        // Make sure all the required members are set on the command
        $userId = $command->getId();
        if (!isset($userId)) {
            throw new InvalidInputException("A User ID is required and was not set.");
        }

        // Make sure the user exists
        /** @var User $queriedUser */
        $queriedUser = $this->userRepository->find($userId);
        if (empty($queriedUser) || $queriedUser->isDeleted()) {
            throw new UserNotFoundException("No User with the given ID was found in the database");
        }

        // Make sure that the authenticated user can view this user's information
        if (!$requestingUser->can(ComponentPolicy::ACTION_VIEW, $queriedUser)) {
            throw new ActionNotPermittedException(
                "The authenticated user does not have permission to view this person's information"
            );
        }

        return $queriedUser;
    }
}