<?php

namespace Math\Model\Vector;

use Math\MathInterface;
use Math\Model\Number\ComparableNumber;
use Math\Model\Number\Number;
use Math\Model\Number\NumberWrapper;

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
interface VectorInterface extends MathInterface, \Iterator
{
    public function __toString();

    /**
     * Will return the NumberWrapper, suitable to safely manipulate the Vector component
     * call get() to receive the actual Number
     *
     * @param int $i
     * @return NumberWrapper
     */
    public function __invoke(int $i);

    /**
     * @param VectorInterface $vector
     * @return bool
     */
    public function equals(VectorInterface $vector);

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
     * Will return an actual Number, __invoke() will return the NumberWrapper suited to safely manipulate the Vector entry
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

    /**
     * @return Number
     */
    public function current();
}