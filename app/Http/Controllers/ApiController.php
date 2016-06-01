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