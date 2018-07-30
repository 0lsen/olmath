<?php

namespace Math;

use Math\Number\Model\Number;

class Result
{
    /** @var string */
    public $original;

    /** @var Number */
    public $result;

    /** @var bool */
    public $dbz;

    function __construct($original, $result, $dbz = false)
    {
        $this->original = $original;
        $this->result = $result;
        $this->dbz = $dbz;
    }
}