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
use App\Repositories\TeamRepository;
use App\Repositories\UserRepository;
use Doctrine\ORM\EntityManager;
use Exception;
use Illuminate\Support\Facades\Auth;


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
     * @return User
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
        /** @var User $requestingUser */
        $requestingUser = Auth::user();
        if (empty($requestingUser)) {
            throw new Exception("Could not get the authenticated user");
        }

        // Make sure all the required members are set on the command
        $teamId = $command->getTeamId();
        if (!isset($teamId)) {
            throw new InvalidInputException("Both a valid team ID and user ID are required");
        }

        // Make sure the team exists
        /** @var Team $team */
        $team = $this->getTeamRepository()->find($teamId);
        if (empty($team)) {
            throw new TeamNotFoundException("No Team with the given ID was found in the database");
        }

        // Make sure that the user own the given team
        if (!$requestingUser->ownsTeam($team)) {
            throw new ActionNotPermittedException("The authenticated user is not the owner of the given team");
        }

        return $team->getUsers()->toArray();
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