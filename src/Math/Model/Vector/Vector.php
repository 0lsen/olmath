<?php

namespace Math\Model\Vector;

use Math\Exception\UnknownOperandException;
use Math\Model\Number\Number;
use Math\Model\Number\NumberWrapper;
use Math\Model\Number\Zero;

class Vector extends AbstractVector
{
    public function __construct(Number ...$entries)
    {
        foreach ($entries as $entry) {
            $this->entries[] = new NumberWrapper($entry->value() ? $entry : Zero::getInstance());
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
            $this->entries = array_fill(0, $this->dim, new NumberWrapper(Zero::getInstance()));
        } else {
            parent::processMultiplyWithScalar($number);
        }
        return $this;
    }

    public function addVector(VectorInterface $vector)
    {
        $this->checkVectorDim($vector);
        foreach ($this->entries as $index => &$entry) {
            $entry->add($vector($index+1));
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
                $this->entries[] = $vector->get_($i+1);
            }
        } elseif ($vector instanceof SparseVector) {
            $indices = $vector->getIndices();
            for ($i = 0; $i < $vector->getDim(); $i++) {
                $this->entries[] = in_array($i, $indices)
                    ? $vector->get_($i+1)
                    : Zero::getInstance();
            }
        } else {
            throw new UnknownOperandException(get_class($vector));
        }
        $this->dim += $vector->getDim();
        return $this;
    }
}