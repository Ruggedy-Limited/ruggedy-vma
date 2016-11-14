<?php

namespace App\Handlers\Commands;

use App\Commands\RemoveFromTeam as RemoveFromTeamCommand;
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

class RemoveFromTeam extends CommandHandler
{
    /** @var UserRepository  */
    protected $userRepository;

    /** @var TeamRepository  */
    protected $teamRepository;

    /** @var EntityManager  */
    protected $em;

    /**
     * RemoveFromTeam constructor.
     *
     * @param UserRepository $userRepository
     * @param TeamRepository $teamRepository
     * @param EntityManager $em
     */
    public function __construct(UserRepository $userRepository, TeamRepository $teamRepository, EntityManager $em)
    {
        $this->userRepository = $userRepository;
        $this->teamRepository = $teamRepository;
        $this->em             = $em;
    }

    /**
     * Process the RemoveFromTeam command
     *
     * @param RemoveFromTeamCommand $command
     * @return mixed
     * @throws ActionNotPermittedException
     * @throws Exception
     * @throws InvalidInputException
     * @throws TeamNotFoundException
     * @throws UserNotFoundException
     * @throws UserNotInTeamException
     */
    public function handle(RemoveFromTeamCommand $command)
    {
        // Get the authenticated user
        $requestingUser = $this->authenticate();

        $teamId = $command->getTeamId();
        $userId = $command->getUserId();

        // Make sure all the required members are set on the command
        if (!isset($teamId, $userId)) {
            throw new InvalidInputException("All parameters are required and must be valid");
        }

        // Check for a valid team
        /** @var Team $team */
        $team = $this->teamRepository->find($teamId);
        if (empty($team)) {
            throw new TeamNotFoundException("No team related to the given team ID was found in the database");
        }

        // Check that the requsting User owns the given team
        if ($requestingUser->cannot(ComponentPolicy::ACTION_DELETE, $team)) {
            throw new ActionNotPermittedException("The authenticated user does not own the given team");
        }

        // Check that the User exists
        /** @var User $user */
        $user = $this->userRepository->find($userId);
        if (empty($user)) {
            throw new UserNotFoundException("No user related to the given user ID was found in the database");
        }

        // Check that the user exists in the team
        if (empty($team->personIsInTeam($user))) {
            throw new UserNotInTeamException("The givn user is not in the given team");
        }

        // Take the User off the given Team
        $user->removeTeam($team);

        // Save the changes to the database
        $this->em->persist($user);
        $this->em->flush($user);

        return collect([
            'user' => $user,
            'team' => $team,
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