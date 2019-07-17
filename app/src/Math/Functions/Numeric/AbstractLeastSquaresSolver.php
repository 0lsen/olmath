<?php


namespace Math\Functions\Numeric;


use Math\Exception\DimensionException;
use Math\Model\Matrix\MatrixInterface;
use Math\Model\Number\ComparableNumber;
use Math\Model\Number\RationalNumber;
use Math\Model\Vector\SparseVector;
use Math\Model\Vector\VectorInterface;

abstract class AbstractLeastSquaresSolver implements LeastSquaresSolver
{
    private static $MAX_IT = 100;
    private static $TOL_PLACES = 100000000;

    protected $maxIt;
    protected $atol;
    protected $btol;
    protected $lambda;

    protected $m;
    protected $n;

    /**
     * AbstractLeastSquaresSolver constructor.
     * @param int $maxIt
     * @param ComparableNumber $atol
     * @param ComparableNumber $btol
     * @param ComparableNumber $lambda
     */
    public function __construct($maxIt = null, $atol = null, $btol = null, $lambda = null)
    {
        $this->maxIt = is_null($maxIt) ? self::$MAX_IT : $maxIt;
        $this->atol = is_null($atol) ? new RationalNumber(1, self::$TOL_PLACES, 1) : $atol;
        $this->btol = is_null($btol) ? new RationalNumber(1, self::$TOL_PLACES, 1) : $btol;
        $this->lambda = $lambda;
    }

    protected function setDim(MatrixInterface $A, VectorInterface $b) {
        list($m,$n) = $A->getDims();
        if ($m != $b->getDim()) {
            throw new DimensionException('matrix ('.$m.','.$n.') does not match vector of size '.$b->getDim());
        }
        $this->m = $m;
        $this->n = $n;
    }

    protected function trivialSolution(MatrixInterface $A, VectorInterface $b) {
        return
            $this->m == $b->getDim() &&
            $this->n >= $this->m &&
            $b->equals($A->multiplyWithVector((clone $b)->appendVector(new SparseVector($this->n-$this->m))))
                ? $b->appendVector(new SparseVector($this->n-$this->m))
                : null;
    }
}