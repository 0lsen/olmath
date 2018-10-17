<?php

namespace Math\Model\Vector;

use Math\Exception\DimensionException;
use Math\Model\Number\Number;
use Math\Model\Number\RationalNumber;

class Vector extends AbstractVector
{
    /** @var Number[] */
    private $entries = [];

    public function __construct(...$entries)
    {
        foreach ($entries as $entry) {
            $this->entries[] = $entry;
        }
        $this->dim = sizeof($this->entries);
    }

    public function norm()
    {
        $sum = new RationalNumber(0);
        foreach ($this->entries as $entry) {
            $sum = $sum->add($entry->normSquared());
        }

        return $sum->squareRoot();
    }

    public function scalarMultiplyWith(Number $number)
    {
        foreach ($this->entries as $entry) {
            $entry->multiplyWith($number);
        }

        return $this;
    }

    public function addVector(VectorInterface $vector)
    {
        if ($this->dim != $vector->getDim()) {
            throw new DimensionException('vector dimensions don\'t fit');
        }
        foreach ($this->entries as $index => &$entry) {
            $entry = $entry->add($vector->get($index));
        }
        return $this;
    }

    public function get(int $i)
    {
        return $this->entries[$i];
    }
}