<?php

namespace Math\Model\Vector;

use Math\MathInterface;
use Math\Model\Number\ComparableNumber;
use Math\Model\Number\Number;

interface VectorInterface extends MathInterface
{
    /**
     * @return ComparableNumber
     */
    public function norm();

    /**
     * @param Number $number
     * @return VectorInterface
     */
    public function scalarMultiplyWith(Number $number);

    /**
     * @param VectorInterface $vector
     * @return VectorInterface
     */
    public function addVector(VectorInterface $vector);

    /**
     * @return int
     */
    public function getDim();

    /**
     * @param int $i
     * @return Number
     */
    public function get(int $i);
}