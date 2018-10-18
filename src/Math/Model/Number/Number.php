<?php

namespace Math\Model\Number;

use Math\Exception\DivisionByZeroException;
use Math\Exception\UnknownOperandException;
use Math\MathInterface;

/**
 * Interface Number
 * @method \Math\Model\Number\Number add_(\Math\Model\Number\Number $number)
 * @method \Math\Model\Number\Number subtract_(\Math\Model\Number\Number $number)
 * @method \Math\Model\Number\Number multiplyWith_(\Math\Model\Number\Number $number)
 * @method \Math\Model\Number\Number divideBy_(\Math\Model\Number\Number $number)
 * @method \Math\Model\Number\Number negative_()
 * @method \Math\Model\Number\Number square_()
 */
interface Number extends MathInterface
{
    public function __toString();

    public function value();

    /**
     * @return double|float|int
     */
    public function absoluteValue();

    /**
     * @param Number $number
     * @return boolean
     * @throws UnknownOperandException
     */
    public function equals(Number $number);

    /**
     * @return Number
     */
    public function negative();

    /**
     * @param Number $number
     * @return Number
     * @throws UnknownOperandException
     */
    public function add(Number $number);

    /**
     * @param Number $number
     * @return Number
     * @throws UnknownOperandException
     */
    public function subtract(Number $number);

    /**
     * @param Number $number
     * @return Number
     * @throws UnknownOperandException
     */
    public function multiplyWith(Number $number);

    /**
     * @param Number $number
     * @return Number
     * @throws UnknownOperandException
     * @throws DivisionByZeroException
     */
    public function divideBy(Number $number);

    /**
     * @return Number
     */
    public function square();

    /**
     * @return ComparableNumber
     */
    public function normSquared();
}