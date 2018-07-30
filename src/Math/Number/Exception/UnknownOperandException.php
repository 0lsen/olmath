<?php

namespace Math\Number\Exception;

use Throwable;

class UnknownOperandException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}