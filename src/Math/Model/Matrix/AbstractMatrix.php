<?php

namespace Math\Model\Matrix;


use Math\MathConstruct;
use Math\Model\Number\Number;

abstract class AbstractMatrix extends MathConstruct implements MatrixInterface
{
    /** @var int */
    protected $dimM;
    /** @var int */
    protected $dimN;
    /** @var Number */
    protected $entries;

    public function __clone()
    {
        foreach ($this->entries as &$entry) {
            $entry = clone $entry;
        }
    }

    protected function processMultiplyWithScalar(Number $number)
    {
        foreach ($this->entries as &$entry) {
            $entry = $entry->multiplyWith($number);
        }
    }
}