<?php

namespace Math\Model\Vector;


use Math\Exception\DimensionException;
use Math\Exception\UnknownOperandException;
use Math\Model\Number\Number;

class SparseVector extends AbstractVector
{
    public function __construct($dim, $entries = [])
    {
        if (max(array_keys($entries)) >= $dim) {
            throw new DimensionException('vector entry index is out of range');
        }
        $this->dim = $dim;
        $this->entries = $entries;
    }

    public function addVector(VectorInterface $vector)
    {
        parent::addVector($vector);
        if ($vector instanceof SparseVector) {
            foreach ($vector->getIndices() as $i) {
                $this->set($i, $this->get($i)->add($vector->get($i)));
            }
        } elseif ($vector instanceof Vector) {
            for ($i = 0; $i < $vector->getDim(); $i++) {
                $this->set($i, $this->get($i)->add($vector->get($i)));
            }
        } else {
            throw new UnknownOperandException(get_class($vector));
        }
        return $this;
    }

    public function getIndices()
    {
        return array_keys($this->entries);
    }

    public function appendNumber(Number $number)
    {
        $this->entries[$this->dim++] = $number;
        return $this;
    }

    public function appendVector(VectorInterface $vector)
    {
        if ($vector instanceof SparseVector) {
            foreach ($vector->getIndices() as $index) {
                $this->entries[$this->dim+$index] = $vector->get_($index);
            }
        } elseif ($vector instanceof Vector) {
            for ($i = 0; $i < $vector->getDim(); $i++) {
                $this->entries[$this->dim+$i] = $vector->get_($i);
            }
        } else {
            throw new UnknownOperandException(get_class($vector));
        }
        $this->dim += $vector->getDim();
        return $this;
    }
}