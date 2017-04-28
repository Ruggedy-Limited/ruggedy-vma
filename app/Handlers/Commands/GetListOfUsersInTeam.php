<?php

namespace App\Handlers\Commands;

use App\Commands\GetListOfUsersInTeam as GetListOfUsersInTeamCommand;
use App\Entities\Team;
use App\Entities\User;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\InvalidInputException;
use App\Exceptions\TeamNotFoundException;
use App\Exceptions\UserNotFoundException;
use App\Exceptions\UserNotInTeamException;
use App\Policies\ComponentPolicy;
use App\Repositories\TeamRepository;
use App\Repositories\UserRepository;
use Doctrine\ORM\EntityManager;
use Exception;


class GetListOfUsersInTeam extends CommandHandler
{
    /** @var TeamRepository  */
    protected $teamRepository;

    /** @var UserRepository  */
    protected $userRepository;
    
    /** @var EntityManager  */
    protected $em;

    /**
     * GetUserInformation constructor.
     *
     * @param TeamRepository $teamRepository
     * @param UserRepository $userRepository
     * @param EntityManager $em
     */
    public function __construct(TeamRepository $teamRepository, UserRepository $userRepository, EntityManager $em)
    {
        $this->teamRepository = $teamRepository;
        $this->userRepository = $userRepository;
        $this->em             = $em;
    }

    /**
     * Process the GetListOfUsersInTeam command
     *
     * @param GetListOfUsersInTeamCommand $command
     * @return array
     * @throws ActionNotPermittedException
     * @throws Exception
     * @throws InvalidInputException
     * @throws TeamNotFoundException
     * @throws UserNotFoundException
     * @throws UserNotInTeamException
     */
    public function handle(GetListOfUsersInTeamCommand $command)
    {
        // Get the authenticated user
        $requestingUser = $this->authenticate();

        // Make sure all the required members are set on the command
        $teamId = $command->getId();
        if (!isset($teamId)) {
            throw new InvalidInputException("Both a valid team ID and user ID are required");
        }

        // Make sure the team exists
        /** @var Team $team */
        $team = $this->teamRepository->find($teamId);
        if (empty($team)) {
            throw new TeamNotFoundException("No Team with the given ID was found in the database");
        }

        // Make sure that the user own the given team
        if ($requestingUser->cannot(ComponentPolicy::ACTION_LIST, $team)) {
            throw new ActionNotPermittedException(
                "The authenticated user does not have permission to list those people"
            );
        }

        return $team->getUsers()->filter(function ($user) {
            return !$user->isDeleted();
        })->toArray();
    }

    /**
     * @return TeamRepository
     */
    public function getTeamRepository()
    {
        return $this->teamRepository;
    }

    /**
     * @param TeamRepository $teamRepository
     */
    public function setTeamRepository($teamRepository)
    {
        $this->teamRepository = $teamRepository;
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