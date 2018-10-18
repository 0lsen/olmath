<?php

namespace Math\Model\Vector;

use Math\Exception\UnknownOperandException;
use Math\Model\Number\Number;
use Math\Model\Number\Zero;

class Vector extends AbstractVector
{
    public function __construct(...$entries)
    {
        foreach ($entries as $entry) {
            $this->entries[] = $entry;
        }
        $this->dim = sizeof($this->entries);
    }

    public function __toString()
    {
        $string = "[ ";
        for ($i = 0; $i < $this->dim; $i++) {
            if ($i) $string .= " ; ";
            $string .= (string) $this->entries[$i];
        }
        return $string . " ]";
    }

    public function multiplyWithScalar(Number $number)
    {
        if ($number instanceof Zero) {
            $this->entries = array_fill(0, $this->dim, Zero::getInstance());
        } else {
            parent::processMultiplyWithScalar($number);
        }
        return $this;
    }

    public function addVector(VectorInterface $vector)
    {
        parent::addVector($vector);
        foreach ($this->entries as $index => &$entry) {
            $entry = $entry->add($vector->get_($index));
        }
        return $this;
    }

    public function appendNumber(Number $number)
    {
        $this->dim++;
        $this->entries[] = $number;
        return $this;
    }

    public function appendVector(VectorInterface $vector)
    {
        if ($vector instanceof Vector) {
            for ($i = 0; $i < $vector->getDim(); $i++) {
                $this->entries[] = $vector->get_($i);
            }
        } elseif ($vector instanceof SparseVector) {
            $indices = $vector->getIndices();
            for ($i = 0; $i < $vector->getDim(); $i++) {
                $this->entries[] = in_array($i, $indices)
                    ? $vector->get_($i)
                    : Zero::getInstance();
            }
        } else {
            throw new UnknownOperandException(get_class($vector));
        }
        $this->dim += $vector->getDim();
        return $this;
    }
}