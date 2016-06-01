<?php

namespace App\Http\Controllers;

use App;
use App\Commands\EditUserAccount;
use App\Commands\GetUserInformation;
use App\Commands\GetListOfUsersInTeam;
use App\Commands\InviteToTeam;
use App\Commands\RemoveFromTeam;
use App\Contracts\CustomLogging;
use App\Contracts\GivesUserFeedback;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\InvalidEmailException;
use App\Exceptions\InvalidInputException;
use App\Exceptions\TeamNotFoundException;
use App\Exceptions\UserNotFoundException;
use App\Exceptions\UserNotInTeamException;
use App\Http\Responses\ErrorResponse;
use App\Models\MessagingModel;
use App\Services\JsonLogService;
use App\Team as EloquentTeam;
use App\User as EloquentUser;
use Doctrine\ORM\ORMException;
use Exception;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Translation\Translator;
use Laravel\Spark\Interactions\Settings\Teams\SendInvitation;
use League\Tactician\CommandBus;
use Monolog\Logger;


/**
 * @Controller(prefix="api")
 * @Middleware("auth:api")
 */
class ApiController extends Controller implements GivesUserFeedback, CustomLogging
{
    const TRANSLATOR_NAMESPACE = 'api';
    
    /** @var  JsonLogService */
    protected $logger;
    /** @var  CommandBus */
    protected $bus;

    /**
     * ApiController constructor.
     *
     * @param Request $request
     * @param Translator $translator
     * @param JsonLogService $logger
     * @param CommandBus $bus
     */
    public function __construct(Request $request, Translator $translator, JsonLogService $logger, CommandBus $bus)
    {
        parent::__construct($request, $translator);
        $this->setLoggerContext($logger);
        $this->setLogger($logger);
        $this->bus = $bus;
    }

    /**
     * Allow a team administrator to add a new user to their team
     *
     * @Post("/user/{teamId}", as="user.invite", where={"teamId": "[0-9]+"})
     *
     * @param $teamId
     * @return ResponseFactory|JsonResponse
     */
    public function inviteToTeam($teamId)
    {
        try {
            // Create a command and send it through the Command Bus
            $command = new InviteToTeam($teamId, $this->getRequest()->json('email', null));
            $invitation = $this->getBus()->handle($command);

            // Hide the team details and return the invitation
            $invitation->setHidden(['team']);
            return response()->json($invitation);

        } catch (InvalidInputException $e) {

            /**
             * Invalid user input provided
             */
            $this->getLogger()->log(Logger::ERROR, "Invalid input", [
                'teamId' => $teamId ?? null,
                'reason' => $e->getMessage(),
                'trace'  => $this->getLogger()->getTraceAsArrayOfLines($e),
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_INVALID_INPUT);

        } catch (TeamNotFoundException $e) {

            /**
             * A related team was not found in the database for the given team ID
             */
            $this->getLogger()->log(Logger::ERROR, "Team not found in database", [
                'teamId' => $teamId,
                'email'  => $this->getRequest()->json('email', null),
                'reason' => $e->getMessage(),
                'trace'  => $this->getLogger()->getTraceAsArrayOfLines($e),
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_SENDING_INVITE_INVALID_TEAM);

        } catch (InvalidEmailException $e) {

            /**
             * Received an invalid email address
             */
            $this->getLogger()->log(Logger::ERROR, "Could not send team invitation", [
                'teamId' => $teamId,
                'email'  => $this->getRequest()->json('email', null),
                'reason' => $e->getMessage(),
                'trace'  => $this->getLogger()->getTraceAsArrayOfLines($e),
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_SENDING_INVITE_INVALID_EMAIL);

        } catch (Exception $e) {

            /**
             * Unknown error
             */
            $this->getLogger()->log(Logger::ERROR, "Could not send team invitation", [
                'teamId' => $teamId,
                'email'  => $this->getRequest()->json('email', null),
                'reason' => $e->getMessage(),
                'trace'  => $this->getLogger()->getTraceAsArrayOfLines($e),
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_SENDING_INVITE_GENERAL);

        }
    }

    /**
     * Allow a team administrator to remove a user from their team
     *
     * @Delete("/user/{teamId}/{userId}", as="user.remove", where={"teamId": "[0-9]+", "userId": "[0-9]+"})
     *
     * @param $teamId
     * @param $userId
     * @return ResponseFactory|JsonResponse
     */
    public function removeFromTeam($teamId, $userId)
    {
        try {
            // Create a command and send it through the Command Bus
            $command = new RemoveFromTeam($teamId, $userId);
            $responseData = $this->getBus()->handle($command);

            return response()->json($responseData);

        } catch (InvalidInputException $e) {

            /**
             * Invalid input given
             */
            $this->getLogger()->log(Logger::ERROR, "Invalid input", [
                'teamId' => $teamId ?? null,
                'userId' => $userId ?? null,
                'reason' => $e->getMessage(),
                'trace'  => $this->getLogger()->getTraceAsArrayOfLines($e),
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_INVALID_INPUT);

        } catch (TeamNotFoundException $e) {

            /**
             * The Team was not found
             */
            $this->getLogger()->log(Logger::ERROR, "Team not found in database", [
                'teamId' => $teamId,
                'userId' => $userId,
                'reason' => $e->getMessage(),
                'trace'  => $this->getLogger()->getTraceAsArrayOfLines($e),
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_TEAM_DOES_NOT_EXIST);

        } catch (UserNotFoundException $e) {

            /**
             * The User was not found
             */
            $this->getLogger()->log(Logger::ERROR, "User not found in database or team", [
                'teamId' => $teamId,
                'userId' => $userId,
                'reason' => $e->getMessage(),
                'trace'  => $this->getLogger()->getTraceAsArrayOfLines($e),
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_TEAM_MEMBER_DOES_NOT_EXIST);

        } catch (UserNotInTeamException $e) {

            /**
             * The given User is not part of the given Team
             */
            $this->getLogger()->log(Logger::ERROR, "The given User is not part of the given Team", [
                'teamId' => $teamId,
                'userId' => $userId,
                'reason' => $e->getMessage(),
                'trace'  => $this->getLogger()->getTraceAsArrayOfLines($e),
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_TEAM_MEMBER_DOES_NOT_EXIST);

        } catch (Exception $e) {

            /**
             * Unknown error
             */
            $this->getLogger()->log(Logger::ERROR, "Could not remove user from team", [
                'teamId' => $teamId,
                'userId' => $userId,
                'reason' => $e->getMessage(),
                'trace'  => $this->getLogger()->getTraceAsArrayOfLines($e),
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_DEFAULT);

        }
    }

    /**
     * Request information about a person on your team
     *
     * @GET("/user/{teamId}/{userId}", as="user.info", where={"teamId": "[0-9]+", "userId": "[0-9]+"})
     *
     * @param $teamId
     * @param $userId
     * @return ResponseFactory|JsonResponse
     */
    public function getUserInformation($teamId, $userId)
    {
        try {

            $command = new GetUserInformation($teamId, $userId);
            $queriedUser = $this->getBus()->handle($command);

            return response()->json($queriedUser);

        } catch (InvalidInputException $e) {

            /**
             * Invalid input was provided
             */
            $this->getLogger()->log(Logger::ERROR, "Invalid input", [
                'teamId' => $teamId ?? null,
                'userId' => $userId ?? null,
                'reason' => $e->getMessage(),
                'trace'  => $this->getLogger()->getTraceAsArrayOfLines($e),
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_INVALID_INPUT);

        } catch (UserNotFoundException $e) {

            /**
             * The User was not found
             */
            $this->getLogger()->log(Logger::ERROR, "Requested user does not exist", [
                'teamId' => $teamId,
                'userId' => $userId,
                'reason' => $e->getMessage(),
                'trace'  => $this->getLogger()->getTraceAsArrayOfLines($e),
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_USER_DOES_NOT_EXIST);

        } catch (TeamNotFoundException $e) {

            /**
             * The Team was not found
             */
            $this->getLogger()->log(Logger::ERROR, "Requested team does not exist", [
                'teamId' => $teamId,
                'userId' => $userId,
                'reason' => $e->getMessage(),
                'trace'  => $this->getLogger()->getTraceAsArrayOfLines($e),
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_TEAM_DOES_NOT_EXIST);
            
        } catch (ActionNotPermittedException $e) {

            /**
             * The authenticated user does not own the specified team
             */
            $this->getLogger()->log(Logger::DEBUG, "Authenticated user does not own the specified team", [
                'teamId' => $teamId,
                'userId' => $userId,
                'reason' => $e->getMessage(),
                'trace'  => $this->getLogger()->getTraceAsArrayOfLines($e),
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_USER_NOT_TEAM_OWNER);

        } catch (UserNotInTeamException $e) {

            /**
             * The User is not in the given Team
             */
            $this->getLogger()->log(Logger::DEBUG, "Given user is not on the specified team", [
                'teamId' => $teamId,
                'userId' => $userId,
                'reason' => $e->getMessage(),
                'trace'  => $this->getLogger()->getTraceAsArrayOfLines($e),
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_TEAM_MEMBER_DOES_NOT_EXIST);

        } catch (Exception $e) {

            /**
             * Unspecified Exception
             */
            $this->getLogger()->log(Logger::ERROR, "Unspecified Exception", [
                'teamId' => $teamId,
                'userId' => $userId,
                'reason' => $e->getMessage(),
                'trace'  => $this->getLogger()->getTraceAsArrayOfLines($e),
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_DEFAULT);
        }
    }

    /**
     * Get a list of team members in a particular team
     *
     * @GET("/users/{teamId}", as="users.info", where={"teamId": "[0-9]+"})
     *
     * @param $teamId
     * @return ResponseFactory|JsonResponse
     */
    public function getListOfUsersInTeam($teamId)
    {
        try {

            $command = new GetListOfUsersInTeam($teamId);
            $users = $this->getBus()->handle($command);

            return response()->json($users);

        } catch (InvalidInputException $e) {

            /**
             * Invalid input was provided
             */
            $this->getLogger()->log(Logger::ERROR, "Invalid input", [
                'teamId' => $teamId ?? null,
                'reason' => $e->getMessage(),
                'trace'  => $this->getLogger()->getTraceAsArrayOfLines($e),
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_INVALID_INPUT);

        } catch (TeamNotFoundException $e) {

            /**
             * The Team was not found
             */
            $this->getLogger()->log(Logger::ERROR, "Requested team does not exist", [
                'teamId' => $teamId,
                'reason' => $e->getMessage(),
                'trace'  => $this->getLogger()->getTraceAsArrayOfLines($e),
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_TEAM_DOES_NOT_EXIST);

        } catch (ActionNotPermittedException $e) {

            /**
             * The authenticated User does not own the given Team
             */
            $this->getLogger()->log(Logger::ERROR, "Authenticated user does not own the specified team", [
                'teamId' => $teamId,
                'reason' => $e->getMessage(),
                'trace'  => $this->getLogger()->getTraceAsArrayOfLines($e),
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_USER_NOT_TEAM_OWNER);

        } catch (Exception $e) {

            /**
             * Unspecified Exception
             */
            $this->getLogger()->log(Logger::ERROR, "Unspecified Exception", [
                'teamId' => $teamId,
                'reason' => $e->getMessage(),
                'trace'  => $this->getLogger()->getTraceAsArrayOfLines($e),
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_DEFAULT);
        }
    }

    /**
     * Edit some attributes of a user account
     *
     * @PUT("/user/{userId}", as="user.edit", where={"userId": "([0-9]+)"})
     *
     * @param $userId
     * @return ResponseFactory|JsonResponse
     */
    public function editUserAccount($userId)
    {
        try {

            $command        = new EditUserAccount($userId, $this->getRequest()->json()->all());
            $requestingUser = $this->getBus()->handle($command);

            return response()->json($requestingUser);

        } catch (InvalidInputException $e) {

            /**
             * Invalid input was provided
             */
            $this->getLogger()->log(Logger::ERROR, "Invalid input", [
                'userId'      => $userId ?? null,
                'requestBody' => $this->getRequest()->json(),
                'reason'      => $e->getMessage(),
                'trace'       => $this->getLogger()->getTraceAsArrayOfLines($e),
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_INVALID_INPUT);

        } catch (UserNotFoundException $e) {

            /**
             * The User was not found
             */
            $this->getLogger()->log(Logger::ERROR, "Requested user does not exist", [
                'userId'      => $userId,
                'requestBody' => $this->getRequest()->json(),
                'reason'      => $e->getMessage(),
                'trace'       => $this->getLogger()->getTraceAsArrayOfLines($e),
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_USER_DOES_NOT_EXIST);

        } catch (ActionNotPermittedException $e) {

            /**
             * The authenticated User does not have permission to edit the given User account
             */
            $this->getLogger()->log(Logger::ERROR, "Authenticated user does not own the specified team", [
                'userId'      => $userId,
                'requestBody' => $this->getRequest()->json(),
                'reason'      => $e->getMessage(),
                'trace'       => $this->getLogger()->getTraceAsArrayOfLines($e),
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_CANNOT_EDIT_ACCOUNT);


        } catch (ORMException $e) {

            /**
             * Database Error
             */
            $this->getLogger()->log(Logger::ERROR, "Could not update user account", [
                'userId'      => $userId,
                'requestBody' => $this->getRequest()->json(),
                'reason'      => $e->getMessage(),
                'trace'       => $this->getLogger()->getTraceAsArrayOfLines($e),
            ]);

            // Handle the case where the changes result in a duplicate email address
            if (preg_match("/Duplicate entry '(.*)' for key 'users_email_unique'/", $e->getMessage())) {
                return $this->generateErrorResponse(MessagingModel::ERROR_ACCOUNT_WITH_EMAIL_ALREADY_EXISTS);
            }

            return $this->generateErrorResponse(MessagingModel::ERROR_DEFAULT);

        } catch (Exception $e) {

            /**
             * Unspecified Exception
             */
            $this->getLogger()->log(Logger::ERROR, "Could not update user account", [
                'userId'      => $userId,
                'requestBody' => $this->getRequest()->json(),
                'reason'      => $e->getMessage(),
                'trace'       => $this->getLogger()->getTraceAsArrayOfLines($e),
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_DEFAULT);

        }
    }

    /**
     * Generate an error response to return to customer
     *
     * @param string $messageKey
     * @return ResponseFactory|JsonResponse
     */
    protected function generateErrorResponse($messageKey = '')
    {
        $translatorNamespace = null;
        if (!method_exists($this, 'getTranslatorNamespace')) {
            return new ErrorResponse(MessagingModel::ERROR_DEFAULT);
        }

        $translatorNamespace = $this->getTranslatorNamespace();
        $message = $this->getTranslator()->get($translatorNamespace . '.' . $messageKey);

        if ($message == 'messages.' . $messageKey) {
            $message = MessagingModel::ERROR_DEFAULT;
        }

        $errorResponse = new ErrorResponse($message);
        return response()->json($errorResponse);
    }

    /**
     * Create a project in the given user account or in the authenticated user's account if no userId is given
     *
     * @POST("/project/{userId?}", as="project.create", where={"userId":"[0-9]+"})
     *
     * @param $userId
     * @return \Illuminate\Contracts\Routing\ResponseFactory
     */
    public function createProject($userId)
    {
        $requestingUser = Auth::user();
        if (empty($userId)) {
            $requestedUser = $requestingUser;
        }

        if (isset($userId)) {
            $requestedUser = User::find($userId);
        }

        if (empty($requestedUser)) {
            $this->getLogger()->log(Logger::ERROR, "Could not get a valid user account", [
                'userId'         => $userId ?: null,
                'requestingUser' => $requestingUser->id ?: null,
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_USER_DOES_NOT_EXIST);
        }

        $projectFields = $this->getRequest()->json()->all();
        $projectFields['user_id'] = $requestedUser->id;

        try {
            $project = Project::forceCreate($projectFields);
        } catch (Exception $e) {
            $this->getLogger()->log(Logger::ERROR, "Unable to create a new project", [
                'userId'        => $requestedUser->id ?: null,
                'projectFields' => $projectFields ?: null,
                'exception'     => $e->getMessage(),
                'trace'         => $this->getLogger()->getTraceAsArrayOfLines($e),
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_COULD_NOT_CREATE_PROJECT);
        }

        return response()->json($project);
    }

    /**
     * Delete a project
     *
     * @DELETE("/project/{projectId}/{confirm?}", as="project.delete", where={"projectId":"[0-9]+", "confirm":"^confirm$"})
     *
     * @param $projectId
     * @param null $confirm
     * @return \Illuminate\Contracts\Routing\ResponseFactory
     */
    public function deleteProject($projectId, $confirm = null)
    {
        if (!isset($projectId)) {
            $this->getLogger()->log(Logger::ERROR, "Invalid input", [
                'reason' => 'Required parameter $projectId is not set',
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_INVALID_INPUT);
        }

        $project = Project::with('owner')->find($projectId);
        if (empty($project)) {
            return $this->generateErrorResponse(MessagingModel::ERROR_PROJECT_DOES_NOT_EXIST);
        }

        $requestingUser = Auth::user();
        $projectOwner = $project->owner;
        if (!isset($projectOwner->id, $requestingUser->id)) {
            $this->getLogger()->log(Logger::ERROR, "Could not get required User ID", [
                'projectOwnerId'   => $project->owner->id ?: null,
                'requestingUserId' => $requestingUser->id ?: null,
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_DEFAULT);
        }


        if ($projectOwner->id !== $requestingUser->id) {
            $this->getLogger()->log(Logger::ALERT, "Attempt to delete project without permission", [
                'requestingUserId' => $requestingUser->id,
                'projectOwnerId'   => $projectOwner->id,
                'projectId'        => $projectId,
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_DELETE_PROJECT_PERMISSION);
        }

        if (empty($confirm)) {
            return $this->generateErrorResponse(MessagingModel::WARNING_DELETING_PROJECT, false);
        }

        try {
            $project->delete();
        } catch (Exception $e) {
            $this->getLogger()->log(Logger::ERROR, "Could not delete Project", [
                'projectId'      => $projectId,
                'confirm'        => $confirm,
                'requestingUser' => $requestingUser->id ?: null,
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_COULD_NOT_DELETE_PROJECT);
        }

        $response = new \stdClass();
        $response->id = $projectId;
        return response()->json($response);
    }

    /**
     * Edit Project Details
     *
     * @PUT("/project/{projectId}", as="project.edit", where={"projectId":"[0-9]+"})
     *
     * @param $projectId
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse
     */
    public function editProject($projectId)
    {
        if (!isset($projectId)) {
            $this->getLogger()->log(Logger::ERROR, "Invalid input", [
                'reason' => 'Required parameter $projectId is not set',
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_INVALID_INPUT);
        }

        /** @var Project $project */
        $project = Project::with('owner')->find($projectId);
        if (empty($project)) {
            return $this->generateErrorResponse(MessagingModel::ERROR_PROJECT_DOES_NOT_EXIST);
        }

        $requestingUser = Auth::user();
        $projectOwner = $project->owner;
        if (!isset($projectOwner->id, $requestingUser->id)) {
            $this->getLogger()->log(Logger::ERROR, "Could not get required User ID", [
                'projectOwnerId'   => $project->owner->id ?: null,
                'requestingUserId' => $requestingUser->id ?: null,
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_DEFAULT);
        }


        if ($projectOwner->id !== $requestingUser->id) {
            $this->getLogger()->log(Logger::ALERT, "Attempt to delete project without permission", [
                'requestingUserId' => $requestingUser->id,
                'projectOwnerId'   => $projectOwner->id,
                'projectId'        => $projectId,
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_EDIT_PROJECT_PERMISSION);
        }

        // Apply the changes to the User Model
        $projectChanges = $this->getRequest()->json()->all();
        $project->fill($projectChanges);

        try {
            $project->save();
        } catch (Exception $e) {
            $this->getLogger()->log(Logger::ERROR, "Could not save changes to Project", [
                'projectId'        => $projectId,
                'requestingUserId' => $requestingUser->id,
                'projectOwnerId'   => $projectOwner->id,
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_DEFAULT);
        }

        return response()->json($project);
    }

    /**
     * Get a list of projects on a particular person's account
     *
     * @GET("/projects/{userId}", as="projects.list", where={"userId":"[0-9]+"})
     *
     * @param $userId
     * @return \Illuminate\Contracts\Routing\ResponseFactory
     */
    public function getProjectsForUser($userId)
    {
        if (!isset($userId)) {
            $this->getLogger()->log(Logger::ERROR, "Invalid input", [
                'reason' => 'Required paramter $userId is not set',
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_INVALID_INPUT);
        }

        $requestingUser = Auth::user();
        if (empty($requestingUser)) {
            $this->getLogger()->log(Logger::ERROR, "Could not get the authenticated user", [
                'requestParams' => $this->getRequest()->all(),
                'userId'        => $userId,
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_DEFAULT);
        }

        if ($requestingUser->id !== intval($userId)) {
            $this->getLogger()->log(Logger::ALERT, "User trying to list another user's Projects", [
                'userId'           => $userId,
                'requestingUserId' => $requestingUser->id,
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_LIST_PROJECTS_PERMISSION);
        }

        return response()->json($requestingUser->projects()->get()->all());
    }










    /**
     * Create a workspace in the given Project
     *
     * @POST("/workspace/{projectId}", as="workspace.create", where={"projectId":"[0-9]+"})
     *
     * @param $projectId
     * @return \Illuminate\Contracts\Routing\ResponseFactory
     */
    public function createWorkspace($projectId)
    {
        if (!isset($projectId)) {
            $this->getLogger()->log(Logger::ERROR, "Invalid input", [
                'reason' => 'Required parameter $workspaceId is not set',
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_INVALID_INPUT);
        }

        $requestingUser = Auth::user();
        if (empty($requestingUser)) {
            $this->getLogger()->log(Logger::ERROR, "Could not get the authenticated user", [
                'requestParams' => $this->getRequest()->all(),
                'projectId'        => $projectId,
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_DEFAULT);
        }

        $project = Project::with('owner')->find($projectId);
        if (empty($project)) {
            $this->getLogger()->log(Logger::ERROR, "Could not find the given project", [
                'projectId'      => $projectId ?: null,
                'requestingUser' => $requestingUser->id ?: null,
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_WORKSPACE_DOES_NOT_EXIST);
        }

        if (!isset($project->owner->id)) {
            $this->getLogger()->log(Logger::ERROR, "Could not get a required user ID", [
                'projectId'        => $projectId,
                'requestingUserId' => $requestingUser->id,
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_DEFAULT);
        }

        $workspaceFields = $this->getRequest()->json()->all();
        $workspaceFields['user_id']    = $project->owner->id;
        $workspaceFields['project_id'] = $projectId;

        try {
            $workspace = Workspace::forceCreate($workspaceFields);
        } catch (Exception $e) {
            $this->getLogger()->log(Logger::ERROR, "Unable to create a new workspace", [
                'userId'          => $requestingUser->id ?: null,
                'workspaceFields' => $workspaceFields ?: null,
                'exception'       => $e->getMessage(),
                'trace'           => $this->getLogger()->getTraceAsArrayOfLines($e),
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_COULD_NOT_CREATE_WORKSPACE);
        }

        return response()->json($workspace);
    }

    /**
     * Delete a workspace
     *
     * @DELETE("/workspace/{workspaceId}/{confirm?}", as="workspace.delete", where={"workspaceId":"[0-9]+", "confirm":"^confirm$"})
     *
     * @param $workspaceId
     * @param null $confirm
     * @return \Illuminate\Contracts\Routing\ResponseFactory
     */
    public function deleteWorkspace($workspaceId, $confirm = null)
    {
        if (!isset($workspaceId)) {
            $this->getLogger()->log(Logger::ERROR, "Invalid input", [
                'reason' => 'Required parameter $workspaceId is not set',
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_INVALID_INPUT);
        }

        $workspace = Workspace::with('owner')->find($workspaceId);
        if (empty($workspace)) {
            return $this->generateErrorResponse(MessagingModel::ERROR_WORKSPACE_DOES_NOT_EXIST);
        }

        $requestingUser = Auth::user();
        $workspaceOwner = $workspace->owner;
        if (!isset($workspaceOwner->id, $requestingUser->id)) {
            $this->getLogger()->log(Logger::ERROR, "Could not get required User ID", [
                'workspaceOwnerId'   => $workspace->owner->id ?: null,
                'requestingUserId' => $requestingUser->id ?: null,
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_DEFAULT);
        }


        if ($workspaceOwner->id !== $requestingUser->id) {
            $this->getLogger()->log(Logger::ALERT, "Attempt to delete workspace without permission", [
                'requestingUserId' => $requestingUser->id,
                'workspaceOwnerId'   => $workspaceOwner->id,
                'workspaceId'        => $workspaceId,
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_DELETE_WORKSPACE_PERMISSION);
        }

        if (empty($confirm)) {
            return $this->generateErrorResponse(MessagingModel::WARNING_DELETING_WORKSPACE, false);
        }

        try {
            $workspace->delete();
        } catch (Exception $e) {
            $this->getLogger()->log(Logger::ERROR, "Could not delete Workspace", [
                'workspaceId'      => $workspaceId,
                'confirm'        => $confirm,
                'requestingUser' => $requestingUser->id ?: null,
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_COULD_NOT_DELETE_WORKSPACE);
        }

        $response = new \stdClass();
        $response->id = $workspaceId;
        return response()->json($response);
    }

    /**
     * Edit Workspace Details
     *
     * @PUT("/workspace/{workspaceId}", as="workspace.edit", where={"workspaceId":"[0-9]+"})
     *
     * @param $workspaceId
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse
     */
    public function editWorkspace($workspaceId)
    {
        if (!isset($workspaceId)) {
            $this->getLogger()->log(Logger::ERROR, "Invalid input", [
                'reason' => 'Required parameter $workspaceId is not set',
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_INVALID_INPUT);
        }

        /** @var Workspace $workspace */
        $workspace = Workspace::with('owner')->find($workspaceId);
        if (empty($workspace)) {
            return $this->generateErrorResponse(MessagingModel::ERROR_WORKSPACE_DOES_NOT_EXIST);
        }

        $requestingUser = Auth::user();
        $workspaceOwner = $workspace->owner;
        if (!isset($workspaceOwner->id, $requestingUser->id)) {
            $this->getLogger()->log(Logger::ERROR, "Could not get required User ID", [
                'workspaceOwnerId'   => $workspace->owner->id ?: null,
                'requestingUserId' => $requestingUser->id ?: null,
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_DEFAULT);
        }


        if ($workspaceOwner->id !== $requestingUser->id) {
            $this->getLogger()->log(Logger::ALERT, "Attempt to delete workspace without permission", [
                'requestingUserId' => $requestingUser->id,
                'workspaceOwnerId'   => $workspaceOwner->id,
                'workspaceId'        => $workspaceId,
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_EDIT_WORKSPACE_PERMISSION);
        }

        // Apply the changes to the User Model
        $workspaceChanges = $this->getRequest()->json()->all();
        $workspace->fill($workspaceChanges);

        try {
            $workspace->save();
        } catch (Exception $e) {
            $this->getLogger()->log(Logger::ERROR, "Could not save changes to Workspace", [
                'workspaceId'        => $workspaceId,
                'requestingUserId' => $requestingUser->id,
                'workspaceOwnerId'   => $workspaceOwner->id,
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_DEFAULT);
        }

        return response()->json($workspace);
    }

    /**
     * Get a list of workspaces on a particular person's account
     *
     * @GET("/workspaces/{projectId}", as="workspaces.list", where={"projectId":"[0-9]+"})
     *
     * @param $projectId
     * @return \Illuminate\Contracts\Routing\ResponseFactory
     */
    public function getWorkspacesForProject($projectId)
    {
        if (!isset($projectId)) {
            $this->getLogger()->log(Logger::ERROR, "Invalid input", [
                'reason' => 'Required paramter $userId is not set',
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_INVALID_INPUT);
        }

        $requestingUser = Auth::user();
        if (empty($requestingUser)) {
            $this->getLogger()->log(Logger::ERROR, "Could not get the authenticated user", [
                'requestParams' => $this->getRequest()->all(),
                'userId'        => $projectId,
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_DEFAULT);
        }

        if ($requestingUser->id !== intval($projectId)) {
            $this->getLogger()->log(Logger::ALERT, "User trying to list another user's Workspaces", [
                'userId'           => $projectId,
                'requestingUserId' => $requestingUser->id,
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_LIST_WORKSPACES_PERMISSION);
        }

        return response()->json($requestingUser->workspaces()->get()->all());
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

    /**
     * @return CommandBus
     */
    public function getBus()
    {
        return $this->bus;
    }

    /**
     * @param CommandBus $bus
     */
    public function setBus($bus)
    {
        $this->bus = $bus;
    }
}