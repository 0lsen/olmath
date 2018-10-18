<?php

namespace Math\Model\Matrix;

use Math\Model\Number\Number;
use Math\Model\Vector\VectorInterface;

/**
 * Interface MatrixInterface
 * @method \Math\Model\Matrix\MatrixInterface transpose_()
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
}