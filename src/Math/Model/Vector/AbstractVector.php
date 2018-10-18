<?php

namespace Math\Model\Vector;

use Math\Exception\DimensionException;
use Math\MathConstruct;
use Math\Model\Number\Number;
use Math\Model\Number\Zero;

/**
 * Class AbstractVector
 * @method \Math\Model\Vector\VectorInterface scalarMultiplyWith_(\Math\Model\Number\Number $number)
 * @method \Math\Model\Vector\VectorInterface addVector_(\Math\Model\Vector\VectorInterface $number)
 * @method \Math\Model\Number\Number get_(int $i)
 */
abstract class AbstractVector extends MathConstruct implements VectorInterface
{
    /** @var int */
    protected $dim;
    /** @var Number[] */
    protected $entries = [];

    public function __clone()
    {
        foreach ($this->entries as &$entry) {
            $entry = clone $entry;
        }
    }

    public function getDim()
    {
        return $this->dim;
    }

    public function norm()
    {
        $sum = Zero::getInstance();
        foreach ($this->entries as $entry) {
            $sum = $sum->add($entry->normSquared());
        }
        return $sum->squareRoot();
    }

    public function scalarMultiplyWith(Number $number)
    {
        foreach ($this->entries as &$entry) {
            $entry = $entry->multiplyWith($number);
        }
        return $this;
    }

    public function addVector(VectorInterface $vector)
    {
        if ($this->dim != $vector->getDim()) {
            throw new DimensionException('vector dimensions don\'t fit');
        }
    }

    public function get(int $i)
    {
        if ($i >= $this->dim) {
            throw new DimensionException('vector entry index is out of range');
        }
        return $this->entries[$i] ?? Zero::getInstance();
    }

    public function set(int $i, Number $number)
    {
        if ($i >= $this->dim) {
            throw new DimensionException('vector entry index is out of range');
        }
        $this->entries[$i] = $number;
        return $this;
    }
}