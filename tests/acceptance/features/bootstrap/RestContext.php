<?php

namespace Tests\Acceptance\Features\Bootstrap;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Exceptions\InvalidConfigurationException;
use App\Exceptions\InvalidResponseException;
use App\Exceptions\HttpException;
use Illuminate\Http\UploadedFile;
use stdClass;
use Exception;
use PHPUnit_Framework_Assert;
use Tests\Mocks\MockUploadedFile;


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
     * @Given /^that I want to get information about a "([^"]*)" on one of my teams$/
     * @Given /^that I want to get information about my "([^"]*)"$/
     * @Given /^that I want to get information about "([^"]*)"$/
     * @Given /^that I want to find a "([^"]*)"$/
     * @param $objectType
     */
    public function thatIWantToFindA($objectType)
    {
        $this->setRestObjectType(ucwords(strtolower($objectType)));
        $this->setRestObjectMethod(self::HTTP_GET);
    }

    /**
     * @Given /^that I want to remove a "([^"]*)" from my team$/
     * @Given /^that I want to delete a "([^"]*)"$/
     * @param $objectType
     */
    public function thatIWantToDeleteA($objectType)
    {
        $this->setRestObjectType(ucwords(strtolower($objectType)));
        $this->setRestObjectMethod(self::HTTP_DELETE);
    }

    /**
     *
     * @Given /^that I want to update my "([^"]*)"$/
     * @Given /^that I want to update a "([^"]*)"$/
     * @param $objectType
     */
    public function thatIWantToUpdateA($objectType)
    {
        $this->setRestObjectType(ucwords(strtolower($objectType)));
        $this->setRestObjectMethod(self::HTTP_PUT);
    }

    /**
     * @Given /^that I want to change a "([^"]*)" to "([^"]*)"$/
     * @Given /^that their "([^"]*)" is "([^"]*)"$/
     * @Given /^that I want to change it's "([^"]*)" to "([^"]*)"$/
     * @Given /^that I want to change my "([^"]*)" to "([^"]*)"$/
     * @Given /^that its "([^"]*)" is "([^"]*)"$/
     * @param $propertyName
     * @param $propertyValue
     */
    public function thatTheItsIs($propertyName, $propertyValue)
    {
        if ($propertyName === 'file') {
            $this->getRestObject()->hasFile = true;
            $propertyValue = $this->getMinkParameter('files_path') . DIRECTORY_SEPARATOR . $propertyValue;
        }

        $propertyValue = $this->convertBoolHelper($propertyValue);
        $propertyValue = $this->convertIntHelper($propertyValue);
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
                // The request contains a file to be uploaded
                if (!empty($this->getRestObject()->hasFile)) {
                    $content   = null;

                    $filePath  = $this->getRestObject()->file;
                    $filename  = basename($filePath);
                    $fileSize  = filesize($filePath);
                    $mimeType  = mime_content_type($filePath);

                    $file      = new MockUploadedFile($filePath, $filename, $mimeType, $fileSize, null, true);
                    $file->openFile();
                    break;
                }

                // No file, normal request with body
                $content = json_encode($this->getRestObject());
                break;
            default:
                throw new HttpException("Unsupported HTTP method '{$this->getRestObjectMethod()}' used.");
                break;
        }

        // Set the content_type of the request when there are files to attach
        $server = [];
        if (!empty($file) && $file instanceof UploadedFile) {
            $server = ['CONTENT_TYPE' => 'multipart/form-data'];
        }

        $request = Request::create($fullUrl, strtoupper($this->getRestObjectMethod()), [], [], [], $server, $content);

        // Attach files when relevant
        if (!empty($file) && $file instanceof UploadedFile) {
            $request->files->add(['file' => $file]);
        }

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
        PHPUnit_Framework_Assert::assertEquals(
            $responseCode,
            $this->getResponse()->getStatusCode()
        );
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
     *  @Given /^the response does not have a "([^"]*)" property$/
     * @param $propertyName
     * @throws InvalidResponseException
     */
    public function theResponseDoesNotHaveAProperty($propertyName)
    {
        $this->theResponseHasAPropertyHelper($propertyName, true);
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
        $value = $this->convertIntHelper($value);
        $propertyValue = $this->convertIntHelper($propertyValue);
        PHPUnit_Framework_Assert::assertEquals($propertyValue, $value);
    }

    /**
     * @Then /^the "([^"]*)" array property has a "([^"]*)" value$/
     */
    public function theArrayPropertyHasTheFollowing($index, $value)
    {
        $responseBody = json_decode($this->getResponse()->content(), true);
        PHPUnit_Framework_Assert::assertNotEmpty($responseBody[$index], "The specified index was not in the response");
        PHPUnit_Framework_Assert::assertContains($value, $responseBody[$index]);
    }

    /**
     * @Then /^the array response has the following items:$/
     *
     * @param TableNode $table
     */
    public function theArrayResponseHasTheFollowingItems(TableNode $table)
    {
        $this->theTypeOfTheResponseIs('array');
        $this->arrayHasTheFollowingItems($table);
    }

    /**
     * @Then /^the "([^"]+)" array property has the following items:$/
     *
     * @param TableNode $table
     */
    public function theArrayPropertyHasTheFollowingItems(string $propertyName, TableNode $table)
    {
        $this->arrayHasTheFollowingItems($table, $propertyName);
    }

    /**
     * Iterate over an array and make sure that it contains the expected values
     *
     * @param TableNode $table
     * @param null $key
     * @throws InvalidResponseException
     */
    protected function arrayHasTheFollowingItems(TableNode $table, $key = null)
    {
        $dataSet = $this->responseIsJsonHelper(true);

        if (!empty($key)) {
            $dataSet = $this->getValueOfProperty($key, $dataSet);
        }

        $this->theTypeIsHelper($dataSet, 'array');

        foreach ($table as $index => $row) {
            $row = $this->sanitiseRowHelper($row);
            foreach ($row as $field => $value) {
                // If we don't care what the exact value is we enter * in the TableNode
                if ($value === "*") {
                    continue;
                }

                PHPUnit_Framework_Assert::assertNotEmpty($dataSet);
                PHPUnit_Framework_Assert::assertArrayHasKey($field, $dataSet[$index]);
                PHPUnit_Framework_Assert::assertEquals($value, $dataSet[$index][$field]);
            }
        }
    }

    /**
     * Get the JSON response as an associative array
     *
     * @return array
     */
    protected function getEncodedResponseHelper()
    {
        return json_decode($this->getResponse()->getContent(), true);
    }

    /**
     * @Then /^the "([^"]*)" property does not equal "([^"]*)"$/
     * @param $propertyName
     * @param $propertyValue
     * @throws InvalidResponseException
     */
    public function thePropertyNotEquals($propertyName, $propertyValue)
    {
        $this->responseIsJsonHelper();
        $value = $this->theResponseHasAPropertyHelper($propertyName);
        $value = $this->convertBoolHelper($value);
        $propertyValue = $this->convertBoolHelper($propertyValue);
        PHPUnit_Framework_Assert::assertNotEquals($propertyValue, $value);
    }

    /**
     * @Given /^the type of the "([^"]*)" property is ([^"]*)$/
     * @param $propertyName
     * @param $typeString
     * @throws InvalidResponseException
     */
    public function theTypeOfThePropertyIs($propertyName, $typeString)
    {
        $value = $this->theResponseHasAPropertyHelper($propertyName);

        if ($typeString == 'boolean') {
            $value = $this->convertBoolHelper($value);
        }

        if ($typeString == 'integer') {
            $value = $this->convertIntHelper($value);
        }

        $this->theTypeIsHelper($value, $typeString);
    }

    /**
     * @Then /^the type of the response is (.*)$/
     *
     * @param $typeString
     * @throws InvalidResponseException
     */
    public function theTypeOfTheResponseIs($typeString)
    {
        $response = $this->responseIsJsonHelper();
        $this->theTypeIsHelper($response, $typeString);
    }

    /**
     * Assert that a value is an array
     *
     * @param $value
     */
    protected function theTypeIsHelper($value, $typeString)
    {
        $typeMatcher = PHPUnit_Framework_Assert::isType($typeString);
        $typeMatcher->evaluate($value, "The response/property is not of the correct type: ".$typeString."!");
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
     *
     * @param bool $assoc
     * @return stdClass|array
     * @throws InvalidResponseException
     */
    protected function responseIsJsonHelper($assoc = false) {
        $data = json_decode($this->getResponse()->content(), $assoc);
        if (empty($data)) {
            throw new InvalidResponseException("Response was not JSON" . PHP_EOL . $this->getResponse()->content());
        }

        return $data;
    }

    /**
     * @param $propertyName
     * @param bool $doesNotHaveAProperty
     * @return mixed
     * @throws InvalidResponseException
     */
    protected function theResponseHasAPropertyHelper($propertyName, bool $doesNotHaveAProperty = false) {
        $data = $this->responseIsJsonHelper(true);
        // Get the property name and the data, taking into account that there may be dot syntax in the property name
        $data = $this->getValueOfProperty($propertyName, $data, $doesNotHaveAProperty);

        // We're checking that $data->$propertyName is NOT set and it is. Throw an exception to fail the test.
        if (!empty($doesNotHaveAProperty) && isset($data)) {
            throw new InvalidResponseException("Property '".$propertyName."' IS set!" . PHP_EOL);
        }

        if (empty($doesNotHaveAProperty) && !isset($data)) {
            throw new InvalidResponseException("Property '".$propertyName."' is NOT set!" . PHP_EOL);
        }

        return $data;
    }

    /**
     * @param $propertyName
     * @param array $data
     * @param bool $doesNotHaveAProperty
     * @return mixed
     * @throws InvalidResponseException
     */
    protected function getValueOfProperty($propertyName, array $data, bool $doesNotHaveAProperty = false)
    {
        // If empty parameters are passed
        if (empty($propertyName) || empty($data)) {
            return null;
        }

        // There is no dot syntax in the property name so we're just looking for a property in the first level
        if (strpos($propertyName, '.') === false) {
            if (empty($doesNotHaveAProperty)) {
                PHPUnit_Framework_Assert::assertArrayHasKey(
                    $propertyName,
                    $data,
                    "Property '".$propertyName."' is NOT set!" . PHP_EOL
                );

                return $data[$propertyName];
            }

            if (!empty($doesNotHaveAProperty)) {
                PHPUnit_Framework_Assert::assertArrayNotHasKey(
                    $propertyName,
                    $data,
                    "Property '".$propertyName."' IS set!" . PHP_EOL
                );

                return null;
            }
        }

        // There is dot syntax in the property name. Check that the property exists, then return the parent level of the
        // object and the last part of the dot syntax string as the property name to check
        $properties         = explode(".", $propertyName);
        $propertyNameResult = end($properties);
        reset($properties);

        foreach ($properties as $property) {
            // If we are looking for a set property and we encounter and unset property at any point while traversing
            // deeper into the object heirarchy, throw an exception
            if (empty($doesNotHaveAProperty)) {
                PHPUnit_Framework_Assert::assertArrayHasKey(
                    $property,
                    $data,
                    "Property '".$property."' is NOT set!" . PHP_EOL
                );
            }

            // We are looking for a property that isn't set so if we encounter an unset property at any point while
            // traversing deeper into the object heirarchy, return the current property and it's parent so we know the
            // step will pass
            if (!empty($doesNotHaveAProperty) && empty($data[$property])) {
                $data = null;
                continue;
            }

            $data = $data[$property];
        }

        return $data;
    }

    /**
     * Assert that an element in the response contains an item
     * @param array $row
     * @param array $response
     */
    protected function isInProperty($index, array $row, array $response)
    {
        PHPUnit_Framework_Assert::assertNotEmpty($response, "Empty Array response given");
        PHPUnit_Framework_Assert::assertArrayHasKey(
            $index,
            $response,
            "The index specified does not exist in the response"
        );
        PHPUnit_Framework_Assert::assertArrayHasKey(
            $index,
            $row,
            "The index specified does not exist in the TableNode"
        );
        PHPUnit_Framework_Assert::assertContains($row[$index], $response[$index]);
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