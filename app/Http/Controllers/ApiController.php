<?php

namespace App\Http\Controllers;

use App;
use App\User;
use App\Team;
use App\Services\JsonLogService;
use Illuminate\Database\Eloquent\Collection;
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
 * @Middelware(["auth:api"])
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
        $this->middleware('auth:api');
    }

    /**
     * Allow a team administrator to add a new user to their team
     *
     * @Post("/user/{teamId}", as="user.invite", where={"teamId": "[0-9]+"})
     *
     * @param $teamId
     * @return \Illuminate\Contracts\Routing\ResponseFactory
     */
    public function inviteToTeam($teamId)
    {
        // Check that the team exists
        $team = Team::find($teamId);
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
            // Send the invitation
            $invitation = $sendInvitation->handle($team, $email);
        } catch (Exception $e) {
            $this->getLogger()->log(Logger::ERROR, "Could not send team invitation", [
                'teamId'    =>  $teamId,
                'email'     => $email,
                'exception' => $e->getMessage(),
                'trace'     => $e->getTraceAsString(),
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
     * @param $teamId
     * @return \Illuminate\Contracts\Routing\ResponseFactory
     */
    public function removeFromTeam($teamId, $userId)
    {
        // Check for a teamId and userId
        if (!isset($teamId, $userId)) {
            $this->getLogger()->log(Logger::ERROR, "Invalid input", [
                'teamId' => $teamId ?? null,
                'userId' => $userId ?? null,
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_INVALID_INPUT);
        }

        // Check for a valid team
        $team = Team::find($teamId);
        if (empty($team)) {
            $this->getLogger()->log(Logger::ERROR, "Team not found in database", [
                'teamId' => $teamId,
                'userId' => $userId,
            ]);
            
            return $this->generateErrorResponse(MessagingModel::ERROR_TEAM_DOES_NOT_EXIST);
        }

        // Check that the User exists
        $user = User::find($userId);
        if (empty($user)) {
            $this->getLogger()->log(Logger::ERROR, "User not found in database or team", [
                'teamId' => $teamId,
                'userId' => $userId,
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_TEAM_MEMBER_DOES_NOT_EXIST);
        }

        try {
            // Check that the user exists in the team
            if (empty($user->teams()->wherePivot('team_id', $teamId)->first()->id)
                || $user->teams()->wherePivot('team_id', $teamId)->first()->id !== intval($teamId)) {
                return $this->generateErrorResponse(MessagingModel::ERROR_TEAM_MEMBER_DOES_NOT_EXIST);
            }

            // Detach the team from the user
            $user->teams()->detach($teamId);
        } catch (Exception $e) {
            $this->getLogger()->log(Logger::ERROR, "Could not remove user from team", [
                'teamId'    => $teamId,
                'userId'    => $userId,
                'reason'    => "Query failed",
                'exception' => $e->getMessage(),
                'trace'     => $e->getTraceAsString(),
            ]);
            
            return $this->generateErrorResponse(MessagingModel::ERROR_DEFAULT);
        }

        // Get the team information and return the user and the team information
        $team = Team::find($teamId);
        
        return response()->json([
            $user->getTable() => $user,
            $team->getTable() => $team
        ]);
        
    }

    /**
     * Request information about a person on your team
     *
     * @GET("/user/{teamId}/{userId}", as="user.info", where={"teamId": "[0-9]+", "userId": "[0-9]+"})
     *
     * @param $teamId
     * @param $userId
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function getUserInformation($teamId, $userId)
    {
        // Make sure the user exists
        /** @var User $queriedUser */
        $queriedUser = User::find($userId);
        if (empty($queriedUser)) {
            $this->getLogger()->log(Logger::ERROR, "Requested user does not exist", [
                'requestParams' => $this->getRequest()->all(),
                'teamId' => $teamId,
                'userId'        => $userId,
            ]);
            
            return $this->generateErrorResponse(MessagingModel::ERROR_USER_DOES_NOT_EXIST);
        }

        // Make sure the team exists
        /** @var Team $team */
        $team = Team::find($teamId);
        if (empty($team)) {
            $this->getLogger()->log(Logger::ERROR, "Requested team does not exist", [
                'requestParams' => $this->getRequest()->all(),
                'teamId' => $teamId,
                'userId'        => $userId,
            ]);
            
            return $this->generateErrorResponse(MessagingModel::ERROR_TEAM_DOES_NOT_EXIST);
        }

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

        if (!$requestingUser->ownsTeam($team)) {
            $this->getLogger()->log(Logger::DEBUG, "Authenticated user does not own the specified team", [
                'requestParams' => $this->getRequest()->all(),
                'teamId'        => $teamId,
                'userId'        => $userId,
            ]);
            
            return $this->generateErrorResponse(MessagingModel::ERROR_USER_NOT_TEAM_OWNER);
        }

        // Make sure the user is on the team
        if (!$queriedUser->onTeam($team)) {
            $this->getLogger()->log(Logger::DEBUG, "Given user is not on the specified team", [
                'requestParams' => $this->getRequest()->all(),
                'teamId'        => $teamId,
                'userId'        => $userId,
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_TEAM_MEMBER_DOES_NOT_EXIST);
        }

        if ($requestingUser->id !== intval($userId)) {
            $queriedUser->setVisible([
                'name', 'email', 'photo_url', 'uses_two_factor_auth',
            ]);
        }

        return response()->json($queriedUser);
    }

    /**
     * Get a list of team members in a particular team
     *
     * @GET("/users/{teamId}", as="users.info", where={"teamId": "[0-9]+"})
     *
     * @param $teamId
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse
     */
    public function getListOfUsersInTeam($teamId)
    {
        // Check that the team exists
        $team = Team::find($teamId);
        if (empty($team)) {
            $this->getLogger()->log(Logger::WARNING, "A team with the given ID could not be found", [
                'teamId' => $teamId,
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_TEAM_DOES_NOT_EXIST);
        }

        $requestingUser = Auth::user();
        if (empty($requestingUser)) {
            $this->getLogger()->log(Logger::ERROR, "Could not get the authenticated user", [
                'requestParams' => $this->getRequest()->all(),
                'teamId'        => $teamId,
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_DEFAULT);
        }

        if (!$requestingUser->ownsTeam($team)) {
            $this->getLogger()->log(Logger::DEBUG, "Authenticated user does not own the specified team", [
                'requestParams' => $this->getRequest()->all(),
                'teamId'        => $teamId,
                'userId'        => $requestingUser->id,
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_USER_NOT_TEAM_OWNER);
        }

        // Get the list of team members and return them
        $teamMembers = $team->users()->get([
            'name', 'email', 'photo_url', 'uses_two_factor_auth',
        ])->all();

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
        /** @var User $requestingUser */
        $requestingUser = Auth::user();
        if (empty($requestingUser)) {
            return $this->generateErrorResponse(MessagingModel::ERROR_USER_DOES_NOT_EXIST);
        }

        if ($requestingUser->id !== intval($userId)) {
            $this->getLogger()->log(Logger::ALERT, "User attempting to edit someone elses account", [
                'requestingUserId' => $requestingUser->id ?: null,
                'requestedUserId'  => $userId,
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_CANNOT_EDIT_ACCOUNT);
        }

        $profileChanges = $this->getRequest()->json()->all();
        $requestingUser->forceFill($profileChanges);

        try {
            $requestingUser->save();
        } catch (Exception $e) {
            if (preg_match("/Duplicate entry '{$requestingUser->email}' for key 'users_email_unique'/", $e->getMessage())) {
                return $this->generateErrorResponse(MessagingModel::ERROR_ACCOUNT_WITH_EMAIL_ALREADY_EXISTS);
            }

            if (preg_match("/Column not found/", $e->getMessage())) {
                return $this->generateErrorResponse(MessagingModel::ERROR_FIELD_DOES_NOT_EXIST);
            }

            $this->getLogger()->log(Logger::ERROR, "Could not update user account", [
                'user'           => $requestingUser->toArray(),
                'profileChanges' => $profileChanges,
                'exception'      => $e->getMessage(),
                'trace'          => $e->getTraceAsString(),
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