<?php

namespace Math\Model\Vector;

use Math\MathInterface;
use Math\Model\Number\ComparableNumber;
use Math\Model\Number\Number;

/**
 * Interface VectorInterface
 * @method \Math\Model\Vector\VectorInterface multiplyWithScalar_(\Math\Model\Number\Number $number)
 * @method \Math\Model\Vector\VectorInterface addVector_(\Math\Model\Vector\VectorInterface $vector)
 * @method \Math\Model\Number\Number get_(int $i)
 * @method \Math\Model\Vector\VectorInterface set_(int $i, \Math\Model\Number\Number $number)
 * @method \Math\Model\Vector\VectorInterface appendNumber_(\Math\Model\Number\Number $number)
 * @method \Math\Model\Vector\VectorInterface appendVector_(\Math\Model\Vector\VectorInterface $vector)
 * @method \Math\Model\Vector\VectorInterface normalise_()
 */
interface VectorInterface extends MathInterface
{
    public function __toString();

    /**
     * @return ComparableNumber
     */
    public function norm();

    /**
     * @param Number $number
     * @return VectorInterface
     */
    public function multiplyWithScalar(Number $number);

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
     * Do NOT use to change the value!
     * Will not affect zero sparse elements and won't work when Number conversion occurs (like adding a Real to a Rational Number).
     *
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

    /**
     * @return VectorInterface
     */
    public function normalise();
}