<?php

namespace App\Http\Controllers;

use App\Http\Responses\ErrorResponse;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use Illuminate\Translation\Translator;
use App\Models\MessagingModel;
use Illuminate\Http\Request;


class Controller extends BaseController
{
    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

    /** @var  Translator */
    protected $translator;
    /** @var  Request */
    protected $request;

    /**
     * @param Request $request
     * @param Translator $translator
     */
    public function __construct(Request $request, Translator $translator)
    {
        $this->request = $request;
        $this->translator = $translator;
    }

    /**
     * Generate an error response to return to customer
     *
     * @param string $messageKey
     * @param bool $isError
     * @return ResponseFactory
     */
    public function generateErrorResponse($messageKey = '', $isError = true)
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
        $errorResponse->setError($isError);
        return response()->json($errorResponse);
    }

    /**
     * @return Translator
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * @param Translator $translator
     */
    public function setTranslator($translator)
    {
        $this->translator = $translator;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param Request $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }
}
