<?php

namespace Math\Number\Model;

use Math\Number\Exception\DivisionByZeroException;
use Math\Number\Exception\UnknownOperandException;

/**
 * Class ComplexNumber
 * @method ComplexNumber add_(Number $number)
 * @method ComplexNumber subtract_(Number $number)
 * @method ComplexNumber multiplyWith_(Number $number)
 * @method ComplexNumber divideBy_(Number $number)
 * @method ComplexNumber negative_()
 */
class ComplexNumber extends AbstractNumber
{
    /** @var RealNumber|RationalNumber */
    public $r;

    /** @var RealNumber|RationalNumber */
    public $i;

    /**
     * ComplexNumber constructor.
     * @param $r
     * @param int $i
     * @throws UnknownOperandException
     * @throws DivisionByZeroException
     */
    public function __construct($r, $i=0)
    {
        if (is_numeric($r) && is_numeric($i)) {
            $this->r = (int)$r == $r
                ? new RationalNumber(abs($r), 1, $r <=> 0)
                : new RealNumber($r);
            $this->i = (int)$i == $i
                ? new RationalNumber(abs($i), 1, $i <=> 0)
                : new RealNumber($i);
        } elseif ($r instanceof ComplexNumber) {
            $this->r = clone $r->r;
            $this->i = clone $r->i;
        } elseif ($r instanceof Number && $i instanceof Number) {
            $this->r = $r;
            $this->i = $i;
        } else {
            throw new UnknownOperandException(get_class($r));
        }
    }

    public function __clone()
    {
        $this->r = clone $this->r;
        $this->i = clone $this->i;
    }

    public function __toString()
    {
        $string = (string) $this->r;
        $i = (string) $this->i;
        if ($i) {
            $string .= (substr($i, 0, 1) == '-' ? "" : "+") . ($i == 1 ? "" : $i) . "i";
        }
        return $string;
    }

    public function value() : ComplexNumber
    {
        return $this;
    }

    public function absoluteValue()
    {
        return sqrt($this->r->square_()->add($this->i->square_())->value());
    }

    public function equals(Number $number)
    {
        if ($number instanceof RealNumber) {
            return $this->r->equals($number) && $this->i->equals(new RealNumber(0));
        } elseif ($number instanceof ComplexNumber) {
            return $this->r->equals($number->r) && $this->i->equals($number->i);
        } else {
            throw new UnknownOperandException(get_class($number));
        }
    }

    public function negative()
    {
        $this->r->negative();
        $this->i->negative();
        return $this;
    }

    public function add(Number $number)
    {
        if ($number instanceof RealNumber) {
            $this->r->add($number);
        } elseif ($number instanceof ComplexNumber) {
            $this->r = $this->r->add_($number->r);
            $this->i = $this->i->add_($number->i);
        } else {
            throw new UnknownOperandException(get_class($number));
        }
        return $this;
    }

    public function subtract(Number $number)
    {
        $this->add($number->negative());
        return $this;
    }

    public function multiplyWith(Number $number)
    {
        if ($number instanceof RealNumber) {
            $this->r->multiplyWith_($number);
            $this->i->multiplyWith_($number);
        } elseif ($number instanceof ComplexNumber) {
            $rOld = clone $this->r;

            $this->r = $this->r->multiplyWith($number->r)->subtract($this->i->multiplyWith_($number->i));
            $this->i = $rOld->multiplyWith($number->i)->add($this->i->multiplyWith($number->r));
        } else {
            throw new UnknownOperandException(get_class($number));
        }
        return $this;
    }

    public function divideBy(Number $number)
    {
        if ($number instanceof ComplexNumber) {
            $rOld = clone $this->r;
            $dividend = $number->r->square_()->add($number->i->square_());

            $this->r = $this->r->multiplyWith($number->r)->add($this->i->multiplyWith_($number->i))->divideBy($dividend);
            $this->i = $number->r->multiplyWith($this->i)->subtract($rOld->multiplyWith($number->i))->divideBy($dividend);
        } elseif ($number instanceof Number) {
            $this->r->divideBy($number);
            $this->i->divideBy($number);
        } else {
            throw new UnknownOperandException(get_class($number));
        }
        return $this;
    }

    public function square() {
        $this->multiplyWith($this);
        return $this;
    }
}