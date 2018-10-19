<?php

namespace Math\Parser;

use Math\Model\Number\Number;

class NumberResult
{
    /** @var string */
    private $original;

    /** @var Number */
    private $result;

    /** @var bool */
    private $dbz;

    function __construct($original, $result, $dbz = false)
    {
        $this->original = $original;
        $this->result = $result;
        $this->dbz = $dbz;
    }

    /**
     * @return string
     */
    public function getOriginal(): string
    {
        return $this->original;
    }

    /**
     * @return Number
     */
    public function getResult(): Number
    {
        return $this->result;
    }

    /**
     * @return bool
     */
    public function isDbz(): bool
    {
        return $this->dbz;
    }
}