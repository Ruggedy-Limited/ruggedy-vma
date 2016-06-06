<?php

namespace App\Http\Controllers;

use App;
use App\Commands\Command;
use App\Commands\CreateProject;
use App\Commands\CreateWorkspace;
use App\Commands\DeleteProject;
use App\Commands\DeleteWorkspace;
use App\Commands\EditProject;
use App\Commands\EditUserAccount;
use App\Commands\GetUserInformation;
use App\Commands\GetListOfUsersInTeam;
use App\Commands\GetListOfUsersProjects;
use App\Commands\InviteToTeam;
use App\Commands\RemoveFromTeam;
use App\Contracts\CustomLogging;
use App\Contracts\GivesUserFeedback;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\InvalidInputException;
use App\Exceptions\UserNotFoundException;
use App\Http\Responses\ErrorResponse;
use App\Models\MessagingModel;
use App\Services\JsonLogService;
use App\Team as EloquentTeam;
use App\User as EloquentUser;
use Exception;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\Model;
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

    /*********
     * USERS *
     *********/

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
        $command = new InviteToTeam($teamId, $this->getRequest()->json('email', null));
        return $this->sendCommandToBusHelper($command, function($invitation)
        {
            // Hide the team details and return the invitation
            /** @var Model $invitation */
            $invitation->setHidden(['team']);
            return $invitation;
        });
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
        $command = new RemoveFromTeam($teamId, $userId);
        return $this->sendCommandToBusHelper($command);
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
        $command = new GetUserInformation($teamId, $userId);
        return $this->sendCommandToBusHelper($command);
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
        $command = new GetListOfUsersInTeam($teamId);
        return $this->sendCommandToBusHelper($command);
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
        $command = new EditUserAccount($userId, $this->getRequest()->json()->all());
        return $this->sendCommandToBusHelper($command);
    }

    /************
     * PROJECTS *
     ************/

    /**
     * Create a project in the given user account or in the authenticated user's account if no userId is given
     *
     * @POST("/project/{userId?}", as="project.create", where={"userId":"[0-9]+"})
     *
     * @param $userId
     * @return ResponseFactory|JsonResponse
     */
    public function createProject($userId)
    {
        $command = new CreateProject($userId, $this->getRequest()->json()->all());
        return $this->sendCommandToBusHelper($command);
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
        $command = new DeleteProject(intval($projectId), boolval($confirm));
        return $this->sendCommandToBusHelper($command);
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
        $command = new EditProject($projectId, $this->getRequest()->json()->all());
        return $this->sendCommandToBusHelper($command);
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
        $command  = new GetListOfUsersProjects(intval($userId));
        return $this->sendCommandToBusHelper($command);
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

    /**************
     * WORKSPACES *
     **************/
    
    /**
     * Create a workspace in the given project
     *
     * @POST("/workspace/{projectId}", as="workspace.create", where={"projectId":"[0-9]+"})
     *
     * @param $projectId
     * @return ResponseFactory|JsonResponse
     */
    public function createWorkspace($projectId)
    {
        $command = new CreateWorkspace($projectId, $this->getRequest()->json()->all());
        return $this->sendCommandToBusHelper($command);
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
        $command = new DeleteWorkspace(intval($workspaceId), boolval($confirm));
        return $this->sendCommandToBusHelper($command);
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
        try {

            $command = new EditWorkspace($workspaceId, $this->getRequest()->json()->all());
            $workspace = $this->getBus()->handle($command);

            return response()->json($workspace);

        } catch (InvalidInputException $e) {

            /**
             * Invalid Input
             */
            $this->getLogger()->log(Logger::ERROR, "Invalid input", [
                'workspaceId'   => $workspaceId ?? null,
                'requestBody' => $this->getRequest()->json()->all(),
                'reason'      => $e->getMessage(),
                'trace'       => $this->getLogger()->getTraceAsArrayOfLines($e),
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_INVALID_INPUT);


        } catch (WorkspaceNotFoundException $e) {

            /**
             * Workspace not found
             */
            $this->getLogger()->log(Logger::ERROR, "Workspace not found", [
                'workspaceId'   => $workspaceId,
                'requestBody' => $this->getRequest()->json()->all(),
                'reason'      => $e->getMessage(),
                'trace'       => $this->getLogger()->getTraceAsArrayOfLines($e),
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_WORKSPACE_DOES_NOT_EXIST);

        } catch (ActionNotPermittedException $e) {

            /**
             * User does not have permission to perform this action
             */
            $this->getLogger()->log(Logger::ERROR, "Permission Denied", [
                'workspaceId'   => $workspaceId,
                'requestBody' => $this->getRequest()->json()->all(),
                'reason'      => $e->getMessage(),
                'trace'       => $this->getLogger()->getTraceAsArrayOfLines($e),
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_EDIT_WORKSPACE_PERMISSION);

        } catch (Exception $e) {

            /**
             * Unspecified Exception
             */
            $this->getLogger()->log(Logger::ERROR, "Could not edit Workspace", [
                'workspaceId'   => $workspaceId,
                'requestBody' => $this->getRequest()->json()->all(),
                'reason'      => $e->getMessage(),
                'trace'       => $this->getLogger()->getTraceAsArrayOfLines($e),
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_DEFAULT);
        }
    }

    /**
     * Get a list of workspaces on a particular person's account
     *
     * @GET("/workspaces/{userId}", as="workspaces.list", where={"userId":"[0-9]+"})
     *
     * @param $userId
     * @return \Illuminate\Contracts\Routing\ResponseFactory
     */
    public function getWorkspacesForUser($userId)
    {
        try {

            $command  = new GetListOfUsersWorkspaces(intval($userId));
            $workspaces = $this->getBus()->handle($command);

            return response()->json($workspaces);

        } catch (InvalidInputException $e) {

            /**
             * Invalid Input
             */
            $this->getLogger()->log(Logger::ERROR, "Invalid input", [
                'userId' => $userId ?? null,
                'reason' => $e->getMessage(),
                'trace'  => $this->getLogger()->getTraceAsArrayOfLines($e),
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_INVALID_INPUT);

        } catch (UserNotFoundException $e) {

            /**
             * The User was not found
             */
            $this->getLogger()->log(Logger::ERROR, "The given User was not found or has been deleted", [
                'userId' => $userId,
                'reason' => $e->getMessage(),
                'trace'  => $this->getLogger()->getTraceAsArrayOfLines($e),
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_USER_DOES_NOT_EXIST);

        } catch (ActionNotPermittedException $e) {

            /**
             * The authenticated User does not have permission to list those Workspaces
             */
            $this->getLogger()->log(Logger::ERROR, "Permission Denied", [
                'userId' => $userId,
                'reason' => $e->getMessage(),
                'trace'  => $this->getLogger()->getTraceAsArrayOfLines($e),
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_LIST_WORKSPACES_PERMISSION);

        } catch (Exception $e) {

            /**
             * Unspecified Exception
             */
            $this->getLogger()->log(Logger::ERROR, "Could not get a list of Workspaces", [
                'userId' => $userId,
                'reason' => $e->getMessage(),
                'trace'  => $this->getLogger()->getTraceAsArrayOfLines($e),
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_DEFAULT);
        }
    }


    /**
     * Send a command over the command bus and handle exceptions
     *
     * @param Command $command
     * @param null $closure
     * @return ResponseFactory|JsonResponse
     */
    protected function sendCommandToBusHelper(Command $command, $closure = null)
    {
        try {
            $result = $this->getBus()->handle($command);

            if (!empty($closure) && is_callable($closure)) {
                $result = $closure($result);
            }

            return response()->json($result);
        } catch (Exception $e) {
            $this->getLogger()->log(Logger::ERROR, "Error processing command", [
                'requestUri'        => $this->getRequest()->getUri(),
                'requestParameters' => $this->getRequest()->all(),
                'requestBody'       => $this->getRequest()->getContent() ?? null,
                'reason'            => $e->getMessage(),
                'trace'             => $this->getLogger()->getTraceAsArrayOfLines($e),
            ]);
            
            return $this->generateErrorResponse(
                MessagingModel::getMessageKeyByExceptionAndCommand($e, $command)
            );
        }
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