<?php

namespace Math\Model\Number;


use Math\Exception\DivisionByZeroException;

class Zero extends AbstractNumber implements ComparableNumber
{
    private static $instance;

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new Zero();
        }
        return self::$instance;
    }
    public function __toString()
    {
        return '0';
    }

    public function value()
    {
        return 0;
    }

    public function absoluteValue()
    {
        return 0;
    }

    public function equals(Number $number)
    {
        return $number->absoluteValue() == 0;
    }

    public function negative()
    {
        return $this;
    }

    public function add(Number $number)
    {
        return clone $number;
    }

    public function subtract(Number $number)
    {
        return $number->negative_();
    }

    public function multiplyWith(Number $number)
    {
        return $this;
    }

    public function divideBy(Number $number)
    {
        if ($number->value() == 0) {
            throw new DivisionByZeroException();
        }
        return $this;
    }

    public function square()
    {
        return $this;
    }

    public function normSquared()
    {
        return $this;
    }

    public function compareTo(ComparableNumber $number)
    {
        return -$number->compareTo($this);
    }

    public function squareRoot()
    {
        return $this;
    }

    public function root($nth)
    {
        return $this;
    }
}