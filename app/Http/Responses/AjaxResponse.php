<?php

namespace App\Http\Responses;

class AjaxResponse extends ErrorResponse
{
    /** @var string */
    protected $html;

    /**
     * AjaxResponse constructor.
     *
     * @param string $message
     * @param string $html
     */
    public function __construct($message = '', $html = '')
    {
        parent::__construct($message);
        $this->html = $html;
    }

    /**
     * @return string
     */
    public function getHtml(): string
    {
        return $this->html;
    }

    /**
     * @param string $html
     */
    public function setHtml(string $html)
    {
        $this->html = $html;
    }
}