<?php

namespace app\Http\Controllers\Api;

use App;
use App\Entities\Base\AbstractEntity;
use App\Http\Controllers\Controller;
use App\Commands\Command;
use App\Contracts\CustomLogging;
use App\Contracts\GivesUserFeedback;
use App\Http\Responses\ErrorResponse;
use App\Models\MessagingModel;
use App\Services\JsonLogService;
use Exception;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Translation\Translator;
use League\Fractal\TransformerAbstract;
use League\Tactician\CommandBus;
use Monolog\Logger;
use Spatie\Fractal\Exceptions\InvalidTransformation;


abstract class AbstractController extends Controller implements GivesUserFeedback, CustomLogging
{
    /** Namespace for the translator */
    const TRANSLATOR_NAMESPACE = 'api';

    const HTTP_METHOD_POST = 'POST';
    const HTTP_METHOD_PUT  = 'PUT';

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

        // Validate the request
        if (!empty($this->getValidationRules())
            && $this->getMethodsRequiringValidation()->contains($request->getMethod())) {

            $this->validate($this->getRequest(), $this->getValidationRules());
        }

        $this->setLoggerContext($logger);

        $this->logger = $logger;
        $this->bus    = $bus;
    }

    /**
     * Send a command over the command bus and handle exceptions
     *
     * @param Command $command
     * @param TransformerAbstract $transformer
     * @param null $closure
     * @return ResponseFactory|JsonResponse
     */
    protected function sendCommandToBusHelper(Command $command, TransformerAbstract $transformer, $closure = null)
    {
        try {
            $result = $this->getBus()->handle($command);

            if (!empty($closure) && is_callable($closure)) {
                $result = $closure($result);
            }

            // Tranform the result and return the response
            $result = $this->transformResult($result, $transformer);
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
     * Transform the command result using a fractal transformer
     *
     * @param $result
     * @param TransformerAbstract $transformer
     * @return string
     */
    protected function transformResult($result, TransformerAbstract $transformer): string
    {
        if ($result instanceof AbstractEntity) {
            return fractal()->item($result, $transformer)->toJson();
        }

        return fractal()->collection($result, $transformer)->toJson();
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
     * Get the validation rules to be applied to requests received by this controller. Can return an empty array for no
     * validation to be done on the requests
     *
     * @return array
     */
    protected abstract function getValidationRules(): array;

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