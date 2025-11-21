<?php

namespace Wasf\Exceptions;

use Exception;

class HttpException extends Exception
{
    protected $statusCode;

    public function __construct($statusCode = 500, $message = '')
    {
        $this->statusCode = $statusCode;
        parent::__construct($message);
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }
}