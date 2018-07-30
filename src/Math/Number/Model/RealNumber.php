<?php

namespace Math\Number\Model;

use Math\Number\Exception\DivisionByZeroException;
use Math\Number\Exception\UnknownOperandException;

class RealNumber extends AbstractNumber implements ComparableNumber
{
    /** @var double|float|int */
    public $r;

    /**
     * RealNumber constructor.
     * @param $r
     * @throws UnknownOperandException
     */
    public function __construct($r)
    {
        if (is_numeric($r)) {
            $this->r = $r;
        }elseif ($r instanceof RealNumber) {
            $this->r = $r->r;
        } else {
            throw new UnknownOperandException(get_class($r));
        }
    }

    public function __toString()
    {
        return (string) $this->r;
    }

    public function value()
    {
        return $this->r;
    }

    public function absoluteValue()
    {
        return abs($this->r);
    }

    public function equals(Number $number)
    {
        $value = $number->value();
        if (is_numeric($value)) {
            return $this->r == $value;
        } else {
            throw new UnknownOperandException(get_class($value));
        }
    }

    public function compareTo(Number $number)
    {
        return $this->value() <=> $number->value();
    }

    public function negative()
    {
        $this->r = -$this->r;
        return $this;
    }

    public function add(Number $number)
    {
        $value = $number->value();
        if (is_numeric($value)) {
            $this->r += $value;
            return $this;
        } else {
            throw new UnknownOperandException(get_class($value));
        }
    }

    public function subtract(Number $number)
    {
        $this->add($number->negative_());
        return $this;
    }

    public function multiplyWith(Number $number)
    {
        $value = $number->value();
        if (is_numeric($value)) {
            $this->r *= $value;
            return $this;
        } else {
            throw new UnknownOperandException(get_class($value));
        }
    }

    public function divideBy(Number $number)
    {
        $value = $number->value();

        if ($value === 0) {
            throw new DivisionByZeroException();
        } elseif (!is_numeric($value)) {
            throw new UnknownOperandException(get_class($value));
        } else {
            $this->r /= $value;
            return $this;
        }
    }

    public function square()
    {
        $this->multiplyWith($this);
        return $this;
    }
}