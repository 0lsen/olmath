<?php

namespace Math\Model\Vector;

use Math\Exception\DimensionException;
use Math\MathConstruct;
use Math\Model\Number\Number;
use Math\Model\Number\Zero;

/**
 * Class AbstractVector
 * @method \Math\Model\Vector\VectorInterface multiplyWithScalar_(\Math\Model\Number\Number $number)
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

    protected function processMultiplyWithScalar(Number $number)
    {
        foreach ($this->entries as &$entry) {
            $entry = $entry->multiplyWith($number);
        }
    }

    protected function checkVectorDim(VectorInterface $vector)
    {
        if ($this->dim != $vector->getDim()) {
            throw new DimensionException('vector dimensions do not fit. '.$this->dim.' expected, '.$vector->getDim().' found.');
        }
    }

    public function get(int $i)
    {
        $this->checkEntryDim($i-1);
        return $this->entries[$i-1] ?? Zero::getInstance();
    }

    public function set(int $i, Number $number)
    {
        $this->checkEntryDim($i);
        $this->entries[$i-1] = $number;
        return $this;
    }

    private function checkEntryDim(int $i)
    {
        if ($i > $this->dim) {
            throw new DimensionException('vector entry index '.$i.' is out of range ('.$this->dim.')');
        }
    }
}