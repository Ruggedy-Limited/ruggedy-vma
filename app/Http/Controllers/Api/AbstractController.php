<?php

namespace app\Http\Controllers\Api;

use App;
use App\Http\Controllers\Controller;
use App\Commands\Command;
use App\Contracts\CustomLogging;
use App\Contracts\GivesUserFeedback;
use App\Http\Responses\ErrorResponse;
use App\Models\MessagingModel;
use App\Services\JsonLogService;
use App\Team as EloquentTeam;
use App\User as EloquentUser;
use Exception;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Translation\Translator;
use Laravel\Spark\Interactions\Settings\Teams\SendInvitation;
use League\Tactician\CommandBus;
use Monolog\Logger;


abstract class AbstractController extends Controller implements GivesUserFeedback, CustomLogging
{
    /** Namespace for the translator */
    const TRANSLATOR_NAMESPACE = 'api';

    /** @var  JsonLogService */
    protected $logger;

    /** @var  CommandBus */
    protected $bus;

    /**
     * AbstractController constructor.
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

        $this->logger = $logger;
        $this->bus    = $bus;
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
     * Generate an error response to return to the customer
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
        $message             = $this->getTranslator()->get($translatorNamespace . '.' . $messageKey);

        // The message was not found in the language file
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