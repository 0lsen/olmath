<?php

namespace Math\Parser;


use Math\Model\Number\Number;

class FormulaResultEntry
{
    /** @var string */
    private static $decimalPoint = '.';

    /** @var string */
    private static $groupSeparator = ',';

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
            return $this->variable . ' = ' . $this->formatResult();
        } else {
            return $this->original . ' = ' . $this->formatResult();
        }
    }

    private function formatResult()
    {
        $string = (string) $this->result;
        $string = str_replace('.', self::$decimalPoint, $string);
        $callback = function ($result){
            $string = $result[0];
            $count = 0;
            do {
                $string = preg_replace('#(?<!^|'.preg_quote(self::$groupSeparator).')(\d{3})(?=$|'.preg_quote(self::$groupSeparator).')#', self::$groupSeparator.'$1', $string, 1, $count);
            } while ($count);
            return $string;
        };
        $string = preg_replace_callback('#(?<=^|[^'.preg_quote(self::$groupSeparator).'\d])\d{4,}#', $callback, $string);
        return $string;
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

    /**
     * @param string $decimalPoint
     */
    public static function setDecimalPoint(string $decimalPoint)
    {
        self::$decimalPoint = $decimalPoint;
    }

    /**
     * @param string $groupSeparator
     */
    public static function setGroupSeparator(string $groupSeparator)
    {
        self::$groupSeparator = $groupSeparator;
    }
}