<?php

namespace Math\Exception;

use Throwable;

class DivisionByZeroException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}