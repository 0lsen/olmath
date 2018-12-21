<?php

namespace Math\Parser;


use Math\Model\Number\Number;

class FormulaResultEntry
{
    /** @var string */
    private $original;

    /** @var Number */
    private $result;

    /** @var bool */
    private $dbz;

    /** @var string */
    private $variable;

    function __construct($original, $result, $dbz = false, $variable = '')
    {
        $this->original = $original;
        $this->result = $result;
        $this->dbz = $dbz;
        $this->variable = $variable;
    }

    public function __toString()
    {
        if ($this->dbz) {
            return "";
        } elseif ($this->variable) {
            return $this->variable . ' = ' . $this->result;
        } else {
            return $this->original . ' = ' . $this->result;
        }
    }


    /**
     * @param string $variable
     */
    public function setVariable(string $variable)
    {
        $this->variable = $variable;
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
    public function getResult()
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

    /**
     * @return string
     */
    public function getVariable(): string
    {
        return $this->variable;
    }
}