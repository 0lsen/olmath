<?php

namespace Math\Model\Vector;

use Math\Exception\DimensionException;
use Math\MathConstruct;
use Math\Model\Number\Number;
use Math\Model\Number\NumberWrapper;
use Math\Model\Number\Zero;

/**
 * Class AbstractVector
 * @method \Math\Model\Vector\VectorInterface multiplyWithScalar_(\Math\Model\Number\Number $number)
 * @method \Math\Model\Vector\VectorInterface addVector_(\Math\Model\Vector\VectorInterface $vector)
 * @method \Math\Model\Number\Number get_(int $i)
 * @method \Math\Model\Vector\VectorInterface set_(int $i, \Math\Model\Number\Number $number)
 * @method \Math\Model\Vector\VectorInterface appendNumber_(\Math\Model\Number\Number $number)
 * @method \Math\Model\Vector\VectorInterface appendVector_(\Math\Model\Vector\VectorInterface $vector)
 * @method \Math\Model\Vector\VectorInterface normalise_()
 */
abstract class AbstractVector extends MathConstruct implements VectorInterface
{
    /** @var int */
    protected $dim;
    /** @var NumberWrapper[] */
    protected $entries = [];

    public function __clone()
    {
        foreach ($this->entries as &$entry) {
            $entry = clone $entry;
        }
    }

    /**
     * @param int $i
     * @return NumberWrapper
     * @throws DimensionException
     */
    public function __invoke(int $i)
    {
        $this->checkEntryDim($i-1);
        return $this->entries[$i-1] ?? new NumberWrapper(Zero::getInstance());
    }

    public function equals(VectorInterface $vector)
    {
        if ($this->dim != $vector->getDim()) return false;
        for ($i = 0; $i < $this->dim; $i++) {
            if ($this($i)->value() != $vector($i)->value()) return false;
        }
        return true;
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
            $entry->multiplyWith($number);
        }
    }

    protected function checkVectorDim(VectorInterface $vector)
    {
        if ($this->dim != $vector->getDim()) {
            throw new DimensionException('vector dimensions do not fit. '.$this->dim.' expected, '.$vector->getDim().' found.');
        }
    }

    /**
     * @param int $i
     * @return Number
     * @throws DimensionException
     */
    public function get(int $i)
    {
        $this->checkEntryDim($i-1);
        return isset($this->entries[$i-1]) ? $this->entries[$i-1]->get() : Zero::getInstance();
    }

    public function set(int $i, Number $number)
    {
        $this->checkEntryDim($i);
        $this->entries[$i-1] = new NumberWrapper($number);
        return $this;
    }

    private function checkEntryDim(int $i)
    {
        if ($i > $this->dim) {
            throw new DimensionException('vector entry index '.$i.' is out of range ('.$this->dim.')');
        }
    }

    public function normalise()
    {
        $norm = $this->norm();
        foreach ($this->entries as &$entry) {
            $entry->divideBy($norm);
        }
        return $this;
    }

    // Iterator Functions

    protected $iteratorPosition = 0;

    public function current()
    {
        return $this->get($this->iteratorPosition+1);
    }

    public function next()
    {
        $this->iteratorPosition++;
    }

    public function key()
    {
        return $this->iteratorPosition;
    }

    public function valid()
    {
        return $this->iteratorPosition < $this->dim && $this->iteratorPosition >= 0;
    }

    public function rewind()
    {
        $this->iteratorPosition = 0;
    }
}