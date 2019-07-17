<?php

namespace Math\Functions\Numeric;


use Math\Exception\DimensionException;
use Math\Model\Matrix\MatrixInterface;
use Math\Model\Vector\SparseVector;
use Math\Model\Vector\VectorInterface;

interface LeastSquaresSolver
{
    /**
     * @param MatrixInterface $A
     * @param VectorInterface $b
     * @return SparseVector|VectorInterface
     * @throws DimensionException
     * @throws \Math\Exception\DivisionByZeroException
     * @throws \Math\Exception\UnknownOperandException
     * @todo reorthogonalisation
     */
    public function solve(MatrixInterface $A, VectorInterface $b);
}