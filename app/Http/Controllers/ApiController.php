<?php

namespace App\Http\Controllers;

use App;
use App\User;
use App\Team;
use App\Project;
use App\Workspace;
use App\Services\JsonLogService;
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
     * @param $teamId
     * @return \Illuminate\Contracts\Routing\ResponseFactory
     */
    public function inviteToTeam($teamId)
    {
        // Make sure we have all the required parameters
        if (!isset($teamId)) {
            $this->getLogger()->log(Logger::ERROR, "Invalid input", [
                'details' => 'The $teamId parameter is required and was not set'
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_INVALID_INPUT);
        }

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
     * @param $teamId
     * @return \Illuminate\Contracts\Routing\ResponseFactory
     */
    public function removeFromTeam($teamId, $userId)
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
                'trace'     => $this->getLogger()->getTraceAsArrayOfLines($e),
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
        if (!$queriedUser->onTeam($team)) {
            $this->getLogger()->log(Logger::DEBUG, "Given user is not on the specified team", [
                'requestParams' => $this->getRequest()->all(),
                'teamId'        => $teamId,
                'userId'        => $userId,
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_TEAM_MEMBER_DOES_NOT_EXIST);
        }

        // Unless the authenticated user is requesting information about their own account, show only certain fields
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
        // Make sure we have all the required parameters
        if (!isset($teamId)) {
            $this->getLogger()->log(Logger::ERROR, "Invalid input", [
                'details' => 'The $teamId parameter is required and was not set'
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_INVALID_INPUT);
        }

        // Check that the team exists
        $team = Team::find($teamId);
        if (empty($team)) {
            $this->getLogger()->log(Logger::WARNING, "A team with the given ID could not be found", [
                'teamId' => $teamId,
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_TEAM_DOES_NOT_EXIST);
        }

        // Check that we can
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
        // Get the authenticated user
        /** @var User $requestingUser */
        $requestingUser = Auth::user();
        if (empty($requestingUser)) {
            return $this->generateErrorResponse(MessagingModel::ERROR_USER_DOES_NOT_EXIST);
        }

        // Check that the user is editing their own account
        if ($requestingUser->id !== intval($userId)) {
            $this->getLogger()->log(Logger::ALERT, "User attempting to edit someone elses account", [
                'requestingUserId' => $requestingUser->id ?: null,
                'requestedUserId'  => $userId,
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_CANNOT_EDIT_ACCOUNT);
        }

        // Apply the changes to the User Model
        $profileChanges = $this->getRequest()->json()->all();
        $requestingUser->forceFill($profileChanges);

        // Save the changes
        try {
            $requestingUser->save();
        } catch (Exception $e) {
            // Handle the case where the changes result in a duplicate email address
            if (preg_match("/Duplicate entry '{$requestingUser->email}' for key 'users_email_unique'/", $e->getMessage())) {
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
}