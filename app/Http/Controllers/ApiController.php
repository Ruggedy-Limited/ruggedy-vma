<?php

namespace App\Http\Controllers;

use App;
use App\User;
use App\Team;
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
        $team = Team::find($teamId);
        if (empty($team)) {
            $this->getLogger()->log(Logger::ERROR, "Team not found in database", [
                'teamId' => $teamId,
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_SENDING_INVITE_INVALID_TEAM);
        }

        /** @var \Illuminate\Contracts\Validation\Validator $validation */
        $validation = Validator::make($this->getRequest()->all(), [
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
        if (!isset($teamId, $userId)) {
            $this->getLogger()->log(Logger::ERROR, "Invalid input", [
                'teamId' => $teamId ?? null,
                'userId' => $userId ?? null,
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_INVALID_INPUT);
        }
        
        $team = Team::find($teamId);
        if (empty($team)) {
            $this->getLogger()->log(Logger::ERROR, "Team not found in database", [
                'teamId' => $teamId,
                'userId' => $userId,
            ]);
            
            return $this->generateErrorResponse(MessagingModel::ERROR_TEAM_DOES_NOT_EXIST);
        }

        $user = User::find($userId);
        if (empty($user)) {
            $this->getLogger()->log(Logger::ERROR, "User not found in database or team", [
                'teamId' => $teamId,
                'userId' => $userId,
            ]);

            return $this->generateErrorResponse(MessagingModel::ERROR_USER_DOES_NOT_EXIST);
        }
        
        try {
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

        $team = Team::find($teamId);
        
        return response()->json([
            $user->getTable() => $user,
            $team->getTable() => $team
        ]);
        
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