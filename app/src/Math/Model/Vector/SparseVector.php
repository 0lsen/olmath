<?php

namespace Math\Model\Vector;


use Math\Exception\DimensionException;
use Math\Exception\UnknownOperandException;
use Math\Model\Number\Number;
use Math\Model\Number\NumberWrapper;
use Math\Model\Number\Zero;

class SparseVector extends AbstractVector
{
    public function __construct($dim, $entries = [])
    {
        //TODO: take in keys from 1:n instead of 0:n-1 and more checks (non-numeric keys and 0)
        if ($entries && max(array_keys($entries)) >= $dim) {
            throw new DimensionException('vector entry index ('.max(array_keys($entries)).') is out of range ('.$dim.').');
        }
        $this->dim = $dim;
        $this->entries = array_map(
            function (Number $entry){return new NumberWrapper($entry->value() ? $entry : Zero::getInstance());},
            $entries
        );
    }

    public function __toString()
    {
        $this->removeZeros();
        $string = "[ ";
        $first = true;
        for ($i = 0; $i < $this->dim; $i++) {
            if (isset($this->entries[$i])) {
                if (!$first) $string .= " ; ";
                else $first = false;
                $string .= $i+1 . ": " . (string) $this->entries[$i];
            }
        }
        return $string . " ]";
    }

    private function removeZeros()
    {
        foreach ($this->entries as $index => $entry) {
            if (!$entry->get()->value()) {
                unset($this->entries[$index]);
            }
        }
    }

    public function multiplyWithScalar(Number $number)
    {
        if ($number instanceof Zero) {
            $this->entries = [];
        } else {
            parent::processMultiplyWithScalar($number);
        }
        return $this;
    }

    public function addVector(VectorInterface $vector)
    {
        $this->checkVectorDim($vector);
        foreach ($vector as $i => $number) {
            if ($number->value()) {
                $this->set($i+1, $this->get($i+1)->add($number));
            }
        }
        return $this;
    }

    /**
     * Will, for now, return internal keys 0:n-1 instead of logical 1:n
     * @return array
     */
    public function getIndices()
    {
        return array_keys($this->entries);
    }

    public function appendNumber(Number $number)
    {
        $this->entries[$this->dim++] = new NumberWrapper($number);
        return $this;
    }

    public function appendVector(VectorInterface $vector)
    {
        if ($vector instanceof SparseVector) {
            foreach ($vector->getIndices() as $index) {
                $this->entries[$this->dim+$index] = clone $vector($index+1);
            }
        } elseif ($vector instanceof Vector) {
            for ($i = 0; $i < $vector->getDim(); $i++) {
                $this->entries[$this->dim+$i] = clone $vector($i);
            }
        } else {
            throw new UnknownOperandException(get_class($vector));
        }
        $this->dim += $vector->getDim();
        return $this;
    }
}