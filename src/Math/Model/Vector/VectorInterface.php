<?php

namespace Math\Model\Vector;

use Math\MathInterface;
use Math\Model\Number\ComparableNumber;
use Math\Model\Number\Number;

/**
 * Interface VectorInterface
 * @method \Math\Model\Vector\VectorInterface scalarMultiplyWith_(\Math\Model\Number\Number $number)
 * @method \Math\Model\Vector\VectorInterface addVector_(\Math\Model\Vector\VectorInterface $number)
 * @method \Math\Model\Number\Number get_(int $i)
 */
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

    /**
     * @param int $i
     * @param Number $number
     * @return VectorInterface
     */
    public function set(int $i, Number $number);

    /**
     * @param Number $number
     * @return VectorInterface
     */
    public function appendNumber(Number $number);

    /**
     * @param VectorInterface $vector
     * @return VectorInterface
     */
    public function appendVector(VectorInterface $vector);
}