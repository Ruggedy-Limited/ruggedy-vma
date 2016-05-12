<?php

namespace Tests\Acceptance\Features\Bootstrap;

use Behat\Behat\Context\Context;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Exceptions\InvalidConfigurationException;
use App\Exceptions\InvalidResponseException;
use App\Exceptions\HttpException;
use Illuminate\Support\Facades\App;
use stdClass;
use Exception;
use PHPUnit_Framework_Assert;
use Symfony\Component\HttpKernel\HttpKernelInterface;


class RestContext extends FeatureContext implements Context
{
    const HTTP_GET    = 'GET';
    const HTTP_POST   = 'POST';
    const HTTP_DELETE = 'DELETE';
    const HTTP_PUT    = 'PUT';

    /** @var stdClass|array */
    protected $restObject;
    /** @var string  */
    protected $restObjectType;
    /** @var string     defaults to GET */
    protected $restObjectMethod = self::HTTP_GET;
    /** @var Response|null */
    protected $response;
    /** @var string  */
    protected $requestUrl;

    /**
     * Initializes context.
     * Every scenario gets it's own instance of the context object.
     */
    public function __construct()
    {
        parent::__construct();
        // Create an empty stdClass object in the restObject property
        $this->restObject = new stdClass();
    }
    /**
     * @Given /^that I want to add a "([^"]*)" to my team$/
     * @Given /^that I want to make a new "([^"]*)"$/
     * @param $objectType
     */
    public function thatIWantToMakeANew($objectType)
    {
        $this->setRestObjectType(ucwords(strtolower($objectType)));
        $this->setRestObjectMethod(self::HTTP_POST);
    }
    /**
     * @Given /^that I want to find a "([^"]*)"$/
     * @param $objectType
     */
    public function thatIWantToFindA($objectType)
    {
        $this->setRestObjectType(ucwords(strtolower($objectType)));
        $this->setRestObjectMethod(self::HTTP_GET);
    }
    /**
     * @Given /^that I want to delete a "([^"]*)"$/
     * @param $objectType
     */
    public function thatIWantToDeleteA($objectType)
    {
        $this->setRestObjectType(ucwords(strtolower($objectType)));
        $this->setRestObjectMethod(self::HTTP_DELETE);
    }
    /**
     * @Given /^that I want to update a "([^"]*)"$/
     * @param $objectType
     */
    public function thatIWantToUpdateA($objectType)
    {
        $this->setRestObjectType(ucwords(strtolower($objectType)));
        $this->setRestObjectMethod(self::HTTP_PUT);
    }
    /**
     * @Given /^that their "([^"]*)" is "([^"]*)"$/
     * @Given /^that its "([^"]*)" is "([^"]*)"$/
     * @param $propertyName
     * @param $propertyValue
     */
    public function thatTheItsIs($propertyName, $propertyValue)
    {
        $propertyValue = $this->convertBoolHelper($propertyValue);
        $this->getRestObject()->$propertyName = $propertyValue;
    }
    /**
     * @When /^I request "([^"]*)"$/
     * @param $uri
     * @return bool
     * @throws Exception
     * @throws HttpException
     * @throws InvalidConfigurationException
     * @throws InvalidResponseException
     */
    public function iRequest($uri)
    {
        if (empty($uri)) {
            throw new Exception("Empty URI in 'I request :uri'");
        }
        $baseUrl = env('APP_URL');
        if (empty($baseUrl)) {
            throw new InvalidConfigurationException("Invalid base APP_BASE_URL");
        }

        $fullUrl = $baseUrl . $uri . '?api_token=' . $this->getApiKey();
        switch (strtoupper($this->getRestObjectMethod())) {
            case self::HTTP_GET:
            case self::HTTP_DELETE:
                $fullUrl .= '&' . http_build_query((array)$this->getRestObject());
                $content = null;
                break;
            case self::HTTP_POST:
            case self::HTTP_PUT:
                $content = json_encode($this->getRestObject());
                break;
            default:
                throw new HttpException("Unsupported HTTP method '{$this->getRestObjectMethod()}' used.");
                break;
        }

        $request = Request::create($fullUrl, strtoupper($this->getRestObjectMethod()), [], [], [], [], $content);
        $response = app()->handle($request);

        if (empty($response)) {
            throw new InvalidResponseException('Empty response received from server when executing request:' . PHP_EOL
                . $this->echoLastResponse());
        }

        $this->setResponse($response);
        return true;
    }
    /**
     * @Then /^the HTTP response code should be ([^"]*)$/
     * @param $responseCode
     */
    public function theHttpResponseCodeShouldBe($responseCode)
    {
        PHPUnit_Framework_Assert::assertEquals($responseCode, $this->getResponse()->getStatusCode());
    }
    /**
     * @Then /^the response is JSON$/
     */
    public function theResponseIsJson()
    {
        $this->responseIsJsonHelper();
    }
    /**
     * @Given /^the response has a "([^"]*)" property$/
     * @param $propertyName
     * @throws InvalidResponseException
     */
    public function theResponseHasAProperty($propertyName)
    {
        $this->theResponseHasAPropertyHelper($propertyName);
    }
    /**
     * @Then /^the "([^"]*)" property equals "([^"]*)"$/
     * @param $propertyName
     * @param $propertyValue
     * @throws InvalidResponseException
     */
    public function thePropertyEquals($propertyName, $propertyValue)
    {
        $this->responseIsJsonHelper();
        $value = $this->theResponseHasAPropertyHelper($propertyName);
        $value = $this->convertBoolHelper($value);
        $propertyValue = $this->convertBoolHelper($propertyValue);
        PHPUnit_Framework_Assert::assertEquals($propertyValue, $value);
    }
    /**
     * @Given /^the type of the "([^"]*)" property is ([^"]*)$/
     * @param $propertyName
     * @param $typeString
     * @throws InvalidResponseException
     */
    public function theTypeOfThePropertyIs($propertyName, $typeString)
    {
        $this->responseIsJsonHelper();
        $value = $this->theResponseHasAPropertyHelper($propertyName);
        $typeMatcher = PHPUnit_Framework_Assert::isType($typeString);
        $typeMatcher->evaluate($value, "Property '".$propertyName."' is not of the correct type: ".$typeString."!");
    }
    /**
     * @Then /^echo last response$/
     */
    public function echoLastResponse()
    {
        echo $this->getRequestUrl() . PHP_EOL;
        echo PHP_EOL . $this->getResponse()->content() . PHP_EOL;
    }
    /**
     * Helper method to determine if the response is JSON
     * @return stdClass
     * @throws Exception
     */
    protected function responseIsJsonHelper() {
        $data = json_decode($this->getResponse()->content());
        if (empty($data)) {
            throw new InvalidResponseException("Response was not JSON" . PHP_EOL . $this->getResponse()->content());
        }
        return $data;
    }
    /**
     * @param $propertyName
     * @return mixed
     * @throws Exception
     */
    protected function theResponseHasAPropertyHelper($propertyName) {
        $data = $this->responseIsJsonHelper();
        if (!isset($data->$propertyName)) {
            throw new InvalidResponseException("Property '".$propertyName."' is not set!" . PHP_EOL);
        }
        return $data->$propertyName;
    }
    /**
     * Helper method to convert 'true' or 'false' passed as strings from the feature file to proper boolean values
     * @param $value
     * @return bool
     */
    protected function convertBoolHelper($value)
    {
        if ($value === 'true') {
            return true;
        }
        if ($value === 'false') {
            return false;
        }
        return $value;
    }
    /**
     * Helper method to convert integer values passed as strings from the feature file to proper integer values
     * @param $value
     * @return int
     */
    protected function convertIntHelper($value)
    {
        if (is_int($value)) {
            return intval($value);
        }
        return $value;
    }
    /**
     * @return stdClass|array
     */
    public function getRestObject()
    {
        return $this->restObject;
    }
    /**
     * @param stdClass|array $restObject
     */
    public function setRestObject($restObject)
    {
        $this->restObject = $restObject;
    }
    /**
     * @return string
     */
    public function getRestObjectType()
    {
        return $this->restObjectType;
    }
    /**
     * @param string $restObjectType
     */
    public function setRestObjectType($restObjectType)
    {
        $this->restObjectType = $restObjectType;
    }
    /**
     * @return string
     */
    public function getRestObjectMethod()
    {
        return $this->restObjectMethod;
    }
    /**
     * @param string $restObjectMethod
     */
    public function setRestObjectMethod($restObjectMethod)
    {
        $this->restObjectMethod = $restObjectMethod;
    }
    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }
    /**
     * @param Response $response
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }
    /**
     * @return string
     */
    public function getRequestUrl()
    {
        return $this->requestUrl;
    }
    /**
     * @param string $requestUrl
     */
    public function setRequestUrl($requestUrl)
    {
        $this->requestUrl = $requestUrl;
    }
}