<?php

namespace App\Http\Controllers;

use App\Entities\Base\AbstractEntity;
use App\Commands\Command;
use App\Contracts\CustomLogging;
use App\Contracts\GivesUserFeedback;
use App\Http\Responses\ErrorResponse;
use App\Models\MessagingModel;
use App\Services\JsonLogService;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Translation\Translator;
use Laracasts\Flash\FlashNotifier;
use League\Fractal\TransformerAbstract;
use League\Tactician\CommandBus;
use Monolog\Logger;

abstract class AbstractController extends Controller implements GivesUserFeedback, CustomLogging
{
    /** Namespace for the translator */
    const TRANSLATOR_NAMESPACE = 'web';

    /** Request type constants */
    const HTTP_METHOD_POST = 'POST';
    const HTTP_METHOD_PUT  = 'PUT';

    /** Flash messenger message type constants */
    const MESSAGE_TYPE_INFO      = 'info';
    const MESSAGE_TYPE_SUCCESS   = 'success';
    const MESSAGE_TYPE_ERROR     = 'error';
    const MESSAGE_TYPE_WARNING   = 'warning';
    const MESSAGE_TYPE_OVERLAY   = 'overlay';
    const MESSAGE_TYPE_IMPORTANT = 'important';

    /** @var  JsonLogService */
    protected $logger;

    /** @var FlashNotifier */
    protected $flashMessenger;

    /** @var  CommandBus */
    protected $bus;

    /** @var Collection */
    protected $messages;

    /**
     * AbstractController constructor.
     *
     * @param Request $request
     * @param Translator $translator
     * @param JsonLogService $logger
     * @param FlashNotifier $flashMessenger
     * @param CommandBus $bus
     */
    public function __construct(
        Request $request, Translator $translator, JsonLogService $logger, FlashNotifier $flashMessenger, CommandBus $bus
    )
    {
        parent::__construct($request, $translator);

        // Add the auth middleware everywhere
        $this->middleware(['auth']);

        // Set a context for the logger
        $this->setLoggerContext($logger);

        // Set all the protected members
        $this->logger         = $logger;
        $this->bus            = $bus;
        $this->flashMessenger = $flashMessenger;
        $this->messages = collect();
    }

    /**
     * Send a command over the command bus and handle exceptions
     *
     * @param Command $command
     * @param null $closure
     * @return mixed
     */
    protected function sendCommandToBusHelper(Command $command, $closure = null)
    {
        try {
            $result = $this->getBus()->handle($command);

            if (!empty($closure) && is_callable($closure)) {
                $result = $closure($result);
            }

            return $result;
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
     * Abstract the creation of a Controller response to avoid a lot of duplication
     *
     * @param $response
     * @param string $viewOrRoute
     * @param array $parameters
     * @param bool $isRedirect
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    protected function controllerResponseHelper(
        $response, string $viewOrRoute, array $parameters = [], bool $isRedirect = false
    )
    {
        // Handle error responses from the bus
        if ($response instanceof ErrorResponse) {
            $this->flashMessenger->error($response->getMessage());
            return redirect()->back()->withInput();
        }

        // Flash messages to the session
        if (!$this->messages->isEmpty()) {
            $this->messages->each(function ($messages, $messageType) {
                /** @var Collection $messages */
                $messages
                    ->unique()
                    ->each(function ($message) use ($messageType) {
                        $this->flashMessenger->$messageType($message);
                    });
            });
        }

        // Redirect when necessary
        if ($isRedirect) {
            return redirect()->route($viewOrRoute, $parameters);
        }

        // Return a view when necessary
        return view($viewOrRoute, $parameters);
    }

    /**
     * Transform the command result using a fractal transformer
     *
     * @param $result
     * @param TransformerAbstract $transformer
     * @return string
     */
    protected function transformResult($result, TransformerAbstract $transformer): string
    {
        $includes = $this->request->get('include', []);
        if ($result instanceof AbstractEntity || $result instanceof Model) {
            return fractal()->parseIncludes($includes)->item($result, $transformer)->toJson();
        }

        return fractal()->parseIncludes($includes)->collection($result, $transformer)->toJson();
    }

    /**
     * Generate an error response to return to the customer
     *
     * @param string $messageKey
     * @return ErrorResponse
     */
    protected function generateErrorResponse($messageKey = '')
    {
        $translatorNamespace = null;
        $errorResponse = new ErrorResponse(MessagingModel::ERROR_DEFAULT);
        if (!method_exists($this, 'getTranslatorNamespace')) {
            return $errorResponse;
        }

        $translatorNamespace = $this->getTranslatorNamespace();
        $message             = $this->getTranslator()->get($translatorNamespace . '.' . $messageKey);

        // The message was not found in the language file
        if ($message == 'messages.' . $messageKey) {
            $message = MessagingModel::ERROR_DEFAULT;
        }

        $errorResponse->setMessage($message);
        return $errorResponse;
    }

    /**
     * Add a message to be flashed to the session
     *
     * @param string $message
     * @param string $messageType
     */
    protected function addMessage(string $message, string $messageType)
    {
        if (empty($message) || !$this->isValidMessageType($messageType)) {
            return;
        }

        if ($this->messages->get($messageType, false) === false) {
            $this->messages->put($messageType, collect([$message]));
            return;
        }

        $this->messages->get($messageType)->push($message);
    }

    /**
     * Get a Collection of valid message types
     *
     * @return Collection
     */
    protected function getValidMessageTypes(): Collection
    {
        return collect([
            self::MESSAGE_TYPE_INFO,
            self::MESSAGE_TYPE_SUCCESS,
            self::MESSAGE_TYPE_ERROR,
            self::MESSAGE_TYPE_WARNING,
            self::MESSAGE_TYPE_OVERLAY,
            self::MESSAGE_TYPE_IMPORTANT,
        ]);
    }

    /**
     * Check if the given type is a valid message type
     *
     * @param string $type
     * @return bool
     */
    protected function isValidMessageType(string $type): bool
    {
        return $this->getValidMessageTypes()->contains($type);
    }

    /**
     * Get the namespace for the translator to find the relevant response message
     *
     * @return string
     */
    public function getTranslatorNamespace(): string {
        return 'web';
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
        return 'web';
    }

    /**
     * @inheritdoc
     */
    public function getLogFilename(): string
    {
        return 'web-controller.json.log';
    }

    /**
     * Get the validation rules to be applied to requests received by this controller. Can return an empty array for no
     * validation to be done on the requests
     *
     * @return array
     */
    protected abstract function getValidationRules(): array;

    /**
     * Get the validation messages to be returned by this controller if any validation rules fail to pass. Can return an
     * empty array for no validation to be done on the requests
     *
     * @return array
     */
    protected abstract function getValidationMessages(): array;

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

    /**
     * @return Collection
     */
    public function getMethodsRequiringValidation()
    {
        return new Collection([
            self::HTTP_METHOD_POST,
            self::HTTP_METHOD_PUT,
        ]);
    }
}