<?php

namespace Math\Model\Number;

use Math\Exception\DivisionByZeroException;
use Math\Exception\UnknownOperandException;
use Math\Functions\CalcUtil;

class RealNumber extends AbstractNumber implements ComparableNumber
{
    /** @var double|float|int */
    private $r;

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

    public function compareTo(ComparableNumber $number)
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
        } elseif ($value instanceof ComplexNumber) {
            return $value->add($this);
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
        } elseif ($value instanceof ComplexNumber) {
            return $value->multiplyWith_($this);
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

    public function square() {
        $this->multiplyWith($this);
        return $this;
    }

    public function squareRoot()
    {
        return $this->root(2);
    }

    public function root($nth)
    {
        $this->r = CalcUtil::nthRoot($this->r, $nth);
        return $this;
    }

    public function normSquared()
    {
        return $this->square_();
    }

    /**
     * @return float|int
     */
    public function getR()
    {
        return $this->r;
    }
}