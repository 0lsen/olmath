<?php

namespace Math\Model\Matrix;

use Math\Model\Number\Number;
use Math\Model\Vector\VectorInterface;

/**
 * Interface MatrixInterface
 * @method \Math\Model\Matrix\MatrixInterface transpose_()
 * @method \Math\Model\Matrix\MatrixInterface multiplyWithScalar_(Number $number)
 * @method \Math\Model\Number\Number get_(int $i, int $j)
 * @method \Math\Model\Vector\VectorInterface getRow_(int $i)
 * @method \Math\Model\Vector\VectorInterface getCol_(int $i)
 * @method \Math\Model\Matrix\MatrixInterface set_(int $i, int $j, Number $number)
 * @method \Math\Model\Matrix\MatrixInterface setRow_(int $i, VectorInterface $vector)
 * @method \Math\Model\Matrix\MatrixInterface setCol_(int $i, VectorInterface $vector)
 * @method \Math\Model\Matrix\MatrixInterface appendRow_(VectorInterface $vector)
 * @method \Math\Model\Matrix\MatrixInterface appendCol_(VectorInterface $vector)
 * @method \Math\Model\Matrix\MatrixInterface removeRow_(int $i)
 * @method \Math\Model\Matrix\MatrixInterface removeCol_(int $i)
 * @method \Math\Model\Matrix\MatrixInterface removeRows_(int ...$indices)
 * @method \Math\Model\Matrix\MatrixInterface removeCols_(int ...$indices)
 * @method \Math\Model\Matrix\MatrixInterface trim_(int $m, int $n)
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
    public function multiplyWithVector(VectorInterface $vector, bool $transposed = false);

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


    /**
     * @param int $i
     * @param int $j
     * @param Number $number
     * @return MatrixInterface
     */
    public function set(int $i, int $j, Number $number);

    /**
     * @param int $i
     * @param VectorInterface $vector
     * @return MatrixInterface
     */
    public function setRow(int $i, VectorInterface $vector);

    /**
     * @param int $i
     * @param VectorInterface $vector
     * @return MatrixInterface
     */
    public function setCol(int $i, VectorInterface $vector);

    /**
     * @param VectorInterface $vector
     * @return MatrixInterface
     */
    public function appendRow(VectorInterface $vector);

    /**
     * @param VectorInterface $vector
     * @return MatrixInterface
     */
    public function appendCol(VectorInterface $vector);

    /**
     * @param int $i
     * @return MatrixInterface
     */
    public function removeRow(int $i);

    /**
     * @param int $i
     * @return MatrixInterface
     */
    public function removeCol(int $i);

    /**
     * @param int ...$indices
     * @return MatrixInterface
     */
    public function removeRows(int ...$indices);

    /**
     * @param int ...$indices
     * @return MatrixInterface
     */
    public function removeCols(int ...$indices);

    /**
     * @param int $m
     * @param int $n
     * @return MatrixInterface
     */
    public function trim(int $m, int $n);
}