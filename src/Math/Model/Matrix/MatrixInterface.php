<?php

namespace Math\Model\Matrix;

use Math\Model\Number\Number;
use Math\Model\Vector\VectorInterface;

/**
 * Interface MatrixInterface
 * @method \Math\Model\Matrix\MatrixInterface transpose_()
 * @method \Math\Model\Matrix\MatrixInterface multiplyWithScalar_(Number $number)
 * @method \Math\Model\Vector\VectorInterface multiplyWithVector_(VectorInterface $vector)
 * @method \Math\Model\Number\Number get_(int $i, int $j)
 * @method \Math\Model\Vector\VectorInterface getRow_(int $i)
 * @method \Math\Model\Vector\VectorInterface getCol_(int $i)
 */
interface MatrixInterface extends \Math\MathInterface
{
    public function __toString();

    /**
     * @return MatrixInterface
     */
    public function transpose();
    /**
     * @param Number $number
     * @return MatrixInterface
     */
    public function multiplyWithScalar(Number $number);

    /**
     * @param VectorInterface $vector
     * @param bool $transposed
     * @return VectorInterface
     */
    public function multiplyWithVector(VectorInterface $vector);

    /**
     * @return int[]
     */
    public function getDims();

    /**
     * Do NOT use to change the value!
     * Will not affect zero sparse elements and won't work when Number conversion occurs (like adding a Real to a Rational Number).
     *
     * @param int $i
     * @param int $j
     * @return Number
     */
    public function get(int $i, int $j);

    /**
     * @param int $i
     * @return VectorInterface
     */
    public function getRow(int $i);

    /**
     * @param int $i
     * @return VectorInterface
     */
    public function getCol(int $i);

//TODO:    public function set(int $m, int $n);

//TODO:    public function setRow(int $i);

//TODO:    public function setCol(int $i);

//TODO:    public function appendRow(VectorInterface $vector);

//TODO:    public function appendCol(VectorInterface $vector);
}