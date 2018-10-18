<?php

namespace Math\Model\Number;

use Math\Exception\DivisionByZeroException;
use Math\Exception\UnknownOperandException;

class ComplexNumber extends AbstractNumber
{
    /** @var ComparableNumber */
    private $r;

    /** @var ComparableNumber */
    private $i;

    /**
     * ComplexNumber constructor.
     * @param $r
     * @param int $i
     * @throws UnknownOperandException
     * @throws DivisionByZeroException
     */
    public function __construct($r, $i=0)
    {
        if ($r instanceof ComplexNumber) {
            $this->r = clone $r->r;
            $this->i = clone $r->i;
        } else {
            $this->setComponent($this->r, $r);
            $this->setComponent($this->i, $i);
        }
    }

    private function setComponent(&$component, $number)
    {
        if (is_numeric($number)) {
            $component = (int)$number == $number
                ? new RationalNumber(abs($number), 1, $number <=> 0)
                : new RealNumber($number);
        } elseif ($number instanceof Number) {
            $component = $number;
        } else {
            throw new UnknownOperandException('Incompatible Class ' . get_class($number));
        }
    }

    public function __clone()
    {
        $this->r = clone $this->r;
        $this->i = clone $this->i;
    }

    public function __toString()
    {
        $r = (string) $this->r;
        $i = (string) $this->i;

        if (!$r && !$i) return "0";

        $string = "";
        if ($r) $string .= $r;
        if ($r && $i) $string .= " ";
        if ($r && $i && substr($i, 0, 1) != "-") $string .= "+ ";
        if ($i) {
            if ($this->i->absoluteValue() != 1) $string .= $i." ";
            elseif ($this->i->value() < 0) $string .= "- ";
            $string .= "i";
        }


        return $string;
    }

    public function value()
    {
        return $this->i->equals(Zero::getInstance()) ? $this->r->value() : $this;
    }

    public function absoluteValue()
    {
        return sqrt($this->r->square_()->add($this->i->square_())->value());
    }

    public function equals(Number $number)
    {
        if ($number instanceof ComplexNumber) {
            return $this->r->equals($number->r) && $this->i->equals($number->i);
        } elseif ($number instanceof Number) {
            return $this->r->equals($number) && $this->i->equals(Zero::getInstance());
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
            $this->r = $this->r->add($number);
        } elseif ($number instanceof ComplexNumber) {
            $this->r = $this->r->add_($number->r);
            $this->i = $this->i->add_($number->i);
        } elseif (!$number instanceof Zero) {
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
            $this->r = $this->r->multiplyWith($number);
            $this->i = $this->i->multiplyWith($number);
        } elseif ($number instanceof RationalNumber) {
            $this->r = $this->r->multiplyWith($number);
            $this->i = $this->i->multiplyWith($number);
        } elseif ($number instanceof ComplexNumber) {
            $rOld = clone $this->r;
            $this->r = $this->r->multiplyWith($number->r)->subtract($this->i->multiplyWith_($number->i));
            $this->i = $rOld->multiplyWith($number->i)->add($this->i->multiplyWith($number->r));
        } elseif ($number instanceof Zero) {
            return Zero::getInstance();
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
            $this->r = $this->r->divideBy($number);
            $this->i = $this->i->divideBy($number);
        } else {
            throw new UnknownOperandException(get_class($number));
        }
        return $this;
    }

    public function square() {
        $this->multiplyWith($this);
        return $this;
    }

    public function normSquared()
    {
        return $this->r->square_()->add($this->i->square_());
    }

    /**
     * @return ComparableNumber
     */
    public function getR(): ComparableNumber
    {
        return $this->r;
    }

    /**
     * @return ComparableNumber
     */
    public function getI(): ComparableNumber
    {
        return $this->i;
    }
}