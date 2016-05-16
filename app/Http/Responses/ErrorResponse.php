<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;


class ErrorResponse implements JsonSerializable, Jsonable
{
    /** @var bool */
    protected $error = true;
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
     * @return mixed
     */
    public function toJson($options = 0)
    {
        return json_encode($this, $options);
    }

    /**
     * Implementation for the JsonSerializable interface
     *
     * @return mixed
     */
    function jsonSerialize()
    {
        return json_encode($this);
    }

    /**
     * @return boolean
     */
    public function isError()
    {
        return $this->error;
    }

    /**
     * @param boolean $error
     */
    public function setError($error)
    {
        $this->error = $error;
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