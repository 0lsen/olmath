<?php

namespace Math\Model\Vector;

use Math\MathConstruct;

/**
 * Class AbstractVector
 * @method \Math\Model\Vector\VectorInterface scalarMultiplyWith_(\Math\Model\Number\Number $number)
 * @method \Math\Model\Vector\VectorInterface addVector_(\Math\Model\Vector\VectorInterface $number)
 */
abstract class AbstractVector extends MathConstruct implements VectorInterface
{
    protected $dim;

    public function getDim()
    {
        return $this->dim;
    }
}