<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;
use stdClass;


class ErrorResponse implements JsonSerializable, Jsonable
{
    /** @var bool */
    protected $isError = true;

    /** @var  string */
    protected $message;

    /**
     * ErrorResponse constructor.
     * @param string $message
     */
    public function __construct(string $message = '')
    {
        if (!empty($message)) {
            $this->message = $message;
        }
    }

    /**
     * Implementation for the Jsonable interface
     *
     * @param int $options
     * @return string
     */
    public function toJson($options = 0)
    {
        $objectForJson = $this->toStdClass();
        return json_encode($objectForJson, $options);
    }

    /**
     * Implementation for the JsonSerializable interface
     *
     * @return mixed
     */
    function jsonSerialize()
    {
        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * Create a stdClass object of $this so that all the members are public and can be converted to a string
     *
     * @return stdClass
     */
    protected function toStdClass()
    {
        $objectForJson = new stdClass();
        foreach ($this as $prop => $val) {
            $objectForJson->$prop = $val;
        }

        return $objectForJson;
    }

    /**
     * @return boolean
     */
    public function isError()
    {
        return $this->isError;
    }

    /**
     * @param boolean $isError
     */
    public function setError($isError)
    {
        $this->isError = $isError;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }
}