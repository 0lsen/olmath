<?php

namespace Math\Model\Matrix;


use Math\Exception\DimensionException;
use Math\MathConstruct;
use Math\Model\Number\Number;
use Math\Model\Vector\VectorInterface;

/**
 * Class AbstractMatrix
 * @method \Math\Model\Matrix\MatrixInterface transpose_()
 * @method \Math\Model\Matrix\MatrixInterface multiplyWithScalar_(Number $number)
 * @method \Math\Model\Matrix\MatrixInterface addMatrix_(MatrixInterface $matrix)
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
abstract class AbstractMatrix extends MathConstruct implements MatrixInterface
{
    /** @var int */
    protected $dimN;
    /** @var int */
    protected $dimM;
    /** @var Number[] */
    protected $entries = [];

    public function __clone()
    {
        foreach ($this->entries as &$entry) {
            $entry = clone $entry;
        }
    }

    protected function processMultiplyWithScalar(Number $number)
    {
        foreach ($this->entries as &$entry) {
            $entry = $entry->multiplyWith($number);
        }
    }

    protected function checkVectorDim(VectorInterface $vector, bool $checkCol = true)
    {
        if (
            ($checkCol && $vector->getDim() != $this->dimN) ||
            (!$checkCol && $vector->getDim() != $this->dimM)
        ) {
            throw new DimensionException('matrix dimensions ('.$this->dimM.','.$this->dimN.') and vector dimension ('.$vector->getDim().') do not match.');
        }
    }

    protected function checkMatrixDims(MatrixInterface $matrix)
    {
        list($m, $n) = $matrix->getDims();
        if ($this->dimM != $m || $this->dimN != $n) {
            throw new DimensionException('matrix dimensions do not match - adding ('.$m.','.$n.') to ('.$this->dimM.','.$this->dimN.').');
        }
    }

    public function getDims()
    {
        return [$this->dimM, $this->dimN];
    }

    protected function checkDims(int $m, int $n)
    {
        if ($m > $this->dimM || $n > $this->dimN) {
            throw new DimensionException('matrix entry indices ('.$m.','.$n.') out of range ('.($this->dimM-1).','.($this->dimN-1).')');
        }
    }

    public function removeRows(int ...$indices)
    {
        rsort($indices);
        foreach ($indices as $i) {
            $this->removeRow($i);
        }
        return $this;
    }

    public function removeCols(int ...$indices)
    {
        rsort($indices);
        foreach ($indices as $i) {
            $this->removeCol($i);
        }
        return $this;
    }
}