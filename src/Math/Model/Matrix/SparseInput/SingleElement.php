<?php

namespace Math\Model\Matrix\SparseInput;


use Math\Model\Number\Number;

class SingleElement
{
    /** @var int */
    private $row;
    /** @var int */
    private $col;
    /** @var Number */
    private $number;

    /**
     * SingleElement constructor.
     * @param int $row
     * @param int $col
     * @param Number $number
     */
    public function __construct(int $row, int $col, Number $number)
    {
        $this->row = $row;
        $this->col = $col;
        $this->number = $number;
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
     * @return Number
     */
    public function getNumber(): Number
    {
        return $this->number;
    }


}