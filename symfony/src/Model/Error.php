<?php

namespace App\Model;

class Error implements ErrorInterface
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    protected $message;

    /**
     * @param string $code
     * @param string $message
     */
    public function __construct(string $code = null, string $message = null)
    {
        $this->code = $code;
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getCode() : string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getMessage() : string
    {
        return $this->message;
    }

    /**
     *
     * @return array
     */
    public function jsonSerialize() : array
    {
        return get_object_vars($this);
    }
}
