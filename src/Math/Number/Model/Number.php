<?php

namespace Math\Number\Model;

use Math\Number\Exception\DivisionByZeroException;
use Math\Number\Exception\UnknownOperandException;

/**
 * Class Number
 * @method Number add_(Number $number)
 * @method Number subtract_(Number $number)
 * @method Number multiplyWith_(Number $number)
 * @method Number divideBy_(Number $number)
 * @method Number negative_()
 * @method Number square_()
 */
interface Number
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
}