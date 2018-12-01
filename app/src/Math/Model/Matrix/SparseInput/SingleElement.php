<?php

namespace Math\Model\Matrix\SparseInput;


use Math\Model\Number\Number;
use Math\Model\Number\NumberWrapper;

class SingleElement
{
    /** @var int */
    private $row;
    /** @var int */
    private $col;
    /** @var NumberWrapper */
    private $number;

    /**
     * SingleElement constructor.
     * @param int $row
     * @param int $col
     * @param Number $number
     */
    public function __construct(int $row, int $col, Number $number)
    {
        $this->row = $row-1;
        $this->col = $col-1;
        $this->number = new NumberWrapper($number);
    }

    /**
     * @return int
     */
    public function getRow(): int
    {
        return $this->row;
    }

    /**
     * @return int
     */
    public function getCol(): int
    {
        return $this->col;
    }

    /**
     * @return NumberWrapper
     */
    public function getNumber(): NumberWrapper
    {
        return $this->number;
    }


}