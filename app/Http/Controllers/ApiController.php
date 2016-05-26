<?php

namespace App\Http\Controllers;

use App;
use App\Team as EloquentTeam;
use App\User as EloquentUser;
use App\Entities\User;
use App\Entities\Team;
use App\Services\JsonLogService;
use App\Repositories\TeamRepository;
use App\Repositories\UserRepository;
use Doctrine\ORM\EntityManager;
use Illuminate\Translation\Translator;
use Illuminate\Http\Request;
use Laravel\Spark\Interactions\Settings\Teams\SendInvitation;
use Illuminate\Support\Facades\Validator;
use App\Contracts\GivesUserFeedback;
use App\Contracts\CustomLogging;
use App\Models\MessagingModel;
use Exception;
use Monolog\Logger;
use Illuminate\Support\Facades\Auth;


/**
 * @Controller(prefix="api")
 * @Middleware("auth:api")
 */
class ApiController extends Controller implements GivesUserFeedback, CustomLogging
{
    const TRANSLATOR_NAMESPACE = 'api';
    
    /** @var  JsonLogService */
    protected $logger;
    
    public function __construct(Request $request, Translator $translator, JsonLogService $logger)
    {
        parent::__construct($request, $translator);
        $this->setLoggerContext($logger);
        $this->setLogger($logger);
    }

    /**
     * Allow a team administrator to add a new user to their team
     *
     * @Post("/user/{teamId}", as="user.invite", where={"teamId": "[0-9]+"})
     *
     * @param TeamRepository $teamRepository
     * @param $teamId
     * @return \Illuminate\Contracts\Routing\ResponseFactory
     */
    public function inviteToTeam(TeamRepository $teamRepository, $teamId)
    {
        // Make sure we have all the required parameters
        if (!isset($teamId)) {
            $this->getLogger()->log(Logger::ERROR, "Invalid input", [
                'details' => 'The $teamId parameter is required and was not set'
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_INVALID_INPUT);
        }

        // Check that the team exists
        /** @var Team $team */
        $team = $teamRepository->find($teamId);
        if (empty($team)) {
            $this->getLogger()->log(Logger::ERROR, "Team not found in database", [
                'teamId' => $teamId,
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_SENDING_INVITE_INVALID_TEAM);
        }

        // Check for a valid email in the POST payload
        /** @var \Illuminate\Contracts\Validation\Validator $validation */
        $email = $this->getRequest()->json('email');
        $validation = Validator::make(['email' => $email], [
            'email' => 'required|email'
        ]);

        $email = $this->getRequest()->json('email');
        if (empty($email) || $validation->fails()) {
            $this->getLogger()->log(Logger::ERROR, "Invalid email address given", [
                'teamId' => $teamId,
                'email'  => $email,
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_SENDING_INVITE_INVALID_EMAIL);
        }

        // Create a SendInvitation interaction
        $sendInvitation = App::make(SendInvitation::class);

        try {
            $eloquentTeam = new EloquentTeam();
            $eloquentTeam->forceFill($team->toArray());
            // Send the invitation
            $invitation = $sendInvitation->handle($eloquentTeam, $email);
        } catch (Exception $e) {
            $this->getLogger()->log(Logger::ERROR, "Could not send team invitation", [
                'teamId'    => $teamId,
                'email'     => $email,
                'exception' => $e->getMessage(),
                'trace'     => $this->getLogger()->getTraceAsArrayOfLines($e),
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_SENDING_INVITE_GENERAL);
        }

        // Hide the team details and return the invitation
        $invitation->setHidden(['team']);
        return response()->json($invitation);
    }

    /**
     * Allow a team administrator to remove a user from their team
     *
     * @Delete("/user/{teamId}/{userId}", as="user.remove", where={"teamId": "[0-9]+", "userId": "[0-9]+"})
     *
     * @param TeamRepository $teamRepository
     * @param UserRepository $userRepository
     * @param $teamId
     * @param $userId
     * @return \Illuminate\Contracts\Routing\ResponseFactory
     */
    public function removeFromTeam(TeamRepository $teamRepository, UserRepository $userRepository, $teamId, $userId)
    {
        // Make sure we have all the required parameters
        if (!isset($teamId, $userId)) {
            $this->getLogger()->log(Logger::ERROR, "Invalid input", [
                'teamId' => $teamId ?? null,
                'userId' => $userId ?? null,
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_INVALID_INPUT);
        }

        // Check for a valid team
        /** @var Team $team */
        $team = $teamRepository->find($teamId);
        if (empty($team)) {
            $this->getLogger()->log(Logger::ERROR, "Team not found in database", [
                'teamId' => $teamId,
                'userId' => $userId,
            ]);
            
            return $this->generateErrorResponse(MessagingModel::ERROR_TEAM_DOES_NOT_EXIST);
        }

        // Check that the User exists
        /** @var User $user */
        $user = $userRepository->find($userId);
        if (empty($user)) {
            $this->getLogger()->log(Logger::ERROR, "User not found in database or team", [
                'teamId' => $teamId,
                'userId' => $userId,
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_TEAM_MEMBER_DOES_NOT_EXIST);
        }

        // Check that the user exists in the team
        if (empty($user->isInTeam($team))) {
            return $this->generateErrorResponse(MessagingModel::ERROR_TEAM_MEMBER_DOES_NOT_EXIST);
        }

        // Detach the team from the user
        $user->removeFromTeam($team);

        try {
            /** @var EntityManager $em */
            $em = app('em');
            $em->persist($user);
            $em->flush($user);
        } catch (Exception $e) {
            $this->getLogger()->log(Logger::ERROR, "Could not remove user from team", [
                'teamId'    => $teamId,
                'userId'    => $userId,
                'reason'    => "Query failed",
                'exception' => $e->getMessage(),
                'trace'     => $this->getLogger()->getTraceAsArrayOfLines($e),
            ]);
            
            return $this->generateErrorResponse(MessagingModel::ERROR_DEFAULT);
        }
        
        return response()->json([
            'user' => $user,
            'team' => $team
        ]);
    }

    /**
     * Request information about a person on your team
     *
     * @GET("/user/{teamId}/{userId}", as="user.info", where={"teamId": "[0-9]+", "userId": "[0-9]+"})
     *
     * @param TeamRepository $teamRepository
     * @param UserRepository $userRepository
     * @param $teamId
     * @param $userId
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function getUserInformation(TeamRepository $teamRepository, UserRepository $userRepository, $teamId, $userId)
    {
        // Make sure we have all the required parameters
        if (!isset($teamId, $userId)) {
            $this->getLogger()->log(Logger::ERROR, "Invalid input", [
                'teamId' => $teamId ?? null,
                'userId' => $userId ?? null,
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_INVALID_INPUT);
        }

        // Make sure the user exists
        /** @var User $queriedUser */
        $queriedUser = $userRepository->find($userId);
        if (empty($queriedUser)) {
            $this->getLogger()->log(Logger::ERROR, "Requested user does not exist", [
                'requestParams' => $this->getRequest()->all(),
                'teamId' => $teamId,
                'userId' => $userId,
            ]);
            
            return $this->generateErrorResponse(MessagingModel::ERROR_USER_DOES_NOT_EXIST);
        }

        // Make sure the team exists
        /** @var Team $team */
        $team = $teamRepository->find($teamId);
        if (empty($team)) {
            $this->getLogger()->log(Logger::ERROR, "Requested team does not exist", [
                'requestParams' => $this->getRequest()->all(),
                'teamId' => $teamId,
                'userId'        => $userId,
            ]);
            
            return $this->generateErrorResponse(MessagingModel::ERROR_TEAM_DOES_NOT_EXIST);
        }

        // Get the authenticated
        /** @var User $requestingUser */
        $requestingUser = Auth::user();
        if (empty($requestingUser)) {
            $this->getLogger()->log(Logger::ERROR, "Could not get the authenticated user", [
                'requestParams' => $this->getRequest()->all(),
                'teamId'        => $teamId,
                'userId'        => $userId,
            ]);
            
            return $this->generateErrorResponse(MessagingModel::ERROR_DEFAULT);
        }

        // Make sure that the user own the given team
        if (!$requestingUser->ownsTeam($team)) {
            $this->getLogger()->log(Logger::DEBUG, "Authenticated user does not own the specified team", [
                'requestParams' => $this->getRequest()->all(),
                'teamId'        => $teamId,
                'userId'        => $userId,
            ]);
            
            return $this->generateErrorResponse(MessagingModel::ERROR_USER_NOT_TEAM_OWNER);
        }

        // Make sure the given user is on the team
        if (empty($queriedUser->isInTeam($team))) {
            $this->getLogger()->log(Logger::DEBUG, "Given user is not on the specified team", [
                'requestParams' => $this->getRequest()->all(),
                'teamId'        => $teamId,
                'userId'        => $userId,
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_TEAM_MEMBER_DOES_NOT_EXIST);
        }

        // Unless the authenticated user is requesting information about their own account, show only certain fields
        if ($requestingUser->getId() !== intval($userId)) {
            $queriedUser = $queriedUser->toJson(0, [
                'name', 'email', 'photoUrl', 'usesTwoFactorAuth',
            ]);
        }

        return response()->json($queriedUser);
    }

    /**
     * Get a list of team members in a particular team
     *
     * @GET("/users/{teamId}", as="users.info", where={"teamId": "[0-9]+"})
     *
     * @param TeamRepository $teamRepository
     * @param $teamId
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse
     */
    public function getListOfUsersInTeam(TeamRepository $teamRepository, $teamId)
    {
        // Make sure we have all the required parameters
        if (!isset($teamId)) {
            $this->getLogger()->log(Logger::ERROR, "Invalid input", [
                'details' => 'The $teamId parameter is required and was not set'
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_INVALID_INPUT);
        }

        // Check that the team exists
        /** @var Team $team */
        $team = $teamRepository->find($teamId);
        if (empty($team)) {
            $this->getLogger()->log(Logger::WARNING, "A team with the given ID could not be found", [
                'teamId' => $teamId,
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_TEAM_DOES_NOT_EXIST);
        }

        // Check that we can
        /** @var User $requestingUser */
        $requestingUser = Auth::user();
        if (empty($requestingUser)) {
            $this->getLogger()->log(Logger::ERROR, "Could not get the authenticated user", [
                'requestParams' => $this->getRequest()->all(),
                'teamId'        => $teamId,
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_DEFAULT);
        }

        // Make sure that the user own the given team
        if (!$requestingUser->ownsTeam($team)) {
            $this->getLogger()->log(Logger::DEBUG, "Authenticated user does not own the specified team", [
                'requestParams' => $this->getRequest()->all(),
                'teamId'        => $teamId,
                'userId'        => $requestingUser->getId(),
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_USER_NOT_TEAM_OWNER);
        }

        // Get the list of team members and return them
        $teamMembers = $team->getUsers();

        return response()->json($teamMembers);
    }

    /**
     * Edit some attributes of a user account
     *
     * @PUT("/user/{userId}", as="user.edit", where={"userId": "([0-9]+)"})
     *
     * @param $userId
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse
     */
    public function editUserAccount($userId)
    {
        // Get the authenticated user
        /** @var User $requestingUser */
        $requestingUser = Auth::user();
        if (empty($requestingUser)) {
            return $this->generateErrorResponse(MessagingModel::ERROR_USER_DOES_NOT_EXIST);
        }

        // Check that the user is editing their own account
        if ($requestingUser->getId() !== intval($userId)) {
            $this->getLogger()->log(Logger::ALERT, "User attempting to edit someone elses account", [
                'requestingUserId' => $requestingUser->getId() ?: null,
                'requestedUserId'  => $userId,
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_CANNOT_EDIT_ACCOUNT);
        }

        // Apply the changes to the User Model
        $profileChanges = $this->getRequest()->json()->all();
        $requestingUser->setFromArray($profileChanges);

        // Save the changes
        try {
            app('em')->persist($requestingUser);
            app('em')->flush($requestingUser);
        } catch (Exception $e) {
            // Handle the case where the changes result in a duplicate email address
            if (preg_match("/Duplicate entry '{$requestingUser->getEmail()}' for key 'users_email_unique'/", $e->getMessage())) {
                return $this->generateErrorResponse(MessagingModel::ERROR_ACCOUNT_WITH_EMAIL_ALREADY_EXISTS);
            }

            // Handle the case where the person supplied a field that doesn't exist in the database
            if (preg_match("/Column not found/", $e->getMessage())) {
                return $this->generateErrorResponse(MessagingModel::ERROR_FIELD_DOES_NOT_EXIST);
            }

            $this->getLogger()->log(Logger::ERROR, "Could not update user account", [
                'user'           => $requestingUser->toArray(),
                'profileChanges' => $profileChanges,
                'exception'      => $e->getMessage(),
                'trace'          => $this->getLogger()->getTraceAsArrayOfLines($e),
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_DEFAULT);
        }

        $requestingUser->setVisible([
            'name', 'email', 'photo_url', 'uses_two_factor_auth', 'created_at', 'updated_at'
        ]);
        
        return response()->json($requestingUser);
    }

    /**
     * Get the namespace for the translator to find the relevant response message
     *
     * @return string
     */
    public function getTranslatorNamespace(): string {
        return 'api';
    }

    /**
     * @inheritdoc
     */
    function setLoggerContext(JsonLogService $logger)
    {
        $directory = $this->getLogContext();
        $logger->setLoggerName($directory);

        $filename  = $this->getLogFilename();
        $logger->setLogFilename($filename);
    }

    /**
     * @inheritdoc
     */
    public function getLogContext(): string
    {
        return 'api';
    }

    /**
     * @inheritdoc
     */
    public function getLogFilename(): string
    {
        return 'api-controller.json.log';
    }

    /**
     * @return JsonLogService
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param JsonLogService $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }
}