<?php

namespace App\Handlers\Commands;

use App\Commands\InviteToTeam as InviteToTeamCommand;
use App\Entities\Team;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\InvalidInputException;
use App\Exceptions\InvalidEmailException;
use App\Exceptions\TeamNotFoundException;
use App\Policies\ComponentPolicy;
use App\Repositories\TeamRepository;
use App\Team as EloquentTeam;
use Exception;
use Illuminate\Validation\Factory;
use Laravel\Spark\Contracts\Interactions\Settings\Teams\SendInvitation;


class InviteToTeam extends CommandHandler
{
    /** @var TeamRepository  */
    protected $repository;

    /** @var Factory  */
    protected $validator;

    /** @var  SendInvitation */
    protected $service;

    /**
     * InviteToTeamCommandHandler constructor.
     *
     * @param TeamRepository $teamRepository
     * @param Factory $validatorFactory
     * @param SendInvitation $sendInvitation
     */
    public function __construct(TeamRepository $teamRepository, Factory $validatorFactory, SendInvitation $sendInvitation)
    {
        $this->repository = $teamRepository;
        $this->validator  = $validatorFactory;
        $this->service    = $sendInvitation;
    }

    /**
     * Process the InviteToTeam Command
     *
     * @param InviteToTeamCommand $command
     * @return mixed
     * @throws Exception
     * @throws InvalidEmailException
     * @throws InvalidInputException
     * @throws TeamNotFoundException
     */
    public function handle(InviteToTeamCommand $command)
    {
        // Get the authenticated user
        $requestingUser = $this->authenticate();

        $teamId = $command->getTeamId();
        $email  = $command->getEmail();
        // Make sure all the required members are set on the command
        if (!isset($teamId, $email)) {
            throw new InvalidInputException("InviteToTeam command must have a valid team ID and email address");
        }

        // Check that the team exists
        /** @var Team $team */
        $team = $this->getRepository()->find($teamId);
        if (empty($team)) {
            throw new TeamNotFoundException("Could not find a team associated with the given ID");
        }

        // Check that the authenticated User owns the given Team
        if ($requestingUser->cannot(ComponentPolicy::ACTION_UPDATE, $team)) {
            throw new ActionNotPermittedException(
                "The authenticated User does not have permission to invite people to this Team"
            );
        }

        // Check for a valid email in the POST payload
        $validation = $this->getValidator()->make(['email' => $email], [
            'email' => 'required|email'
        ]);
        
        if (empty($email) || $validation->fails()) {
            throw new InvalidEmailException();
        }

        $eloquentTeam = new EloquentTeam();
        $eloquentTeam->forceFill($team->toArray());
        
        // Send the invitation and return an instance
        return $this->getService()->handle($eloquentTeam, $email);
    }

    /**
     * @return TeamRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param TeamRepository $repository
     */
    public function setRepository($repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return Factory
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * @param Factory $validator
     */
    public function setValidator($validator)
    {
        $this->validator = $validator;
    }

    /**
     * @return SendInvitation
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @param SendInvitation $service
     */
    public function setService($service)
    {
        $this->service = $service;
    }
}