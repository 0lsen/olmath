<?php

namespace Math\Model\Matrix;


use Math\Exception\DimensionException;
use Math\Model\Matrix\SparseInput\SingleElement;
use Math\Model\Number\Number;
use Math\Model\Number\Zero;
use Math\Model\Vector\Vector;
use Math\Model\Vector\VectorInterface;

/**
 * Class SparseMatrix
 * @method \Math\Model\Matrix\MatrixInterface transpose_()
 */
class SparseMatrix extends AbstractMatrix
{
    /** @var int[] */
    private $rowIndices;
    /** @var int[] */
    private $colIndices;

    public function __construct(int $n, int $m, SingleElement ...$entries)
    {
        $this->dimN = $n;
        $this->dimM = $m;

        foreach ($entries as $entry) {
            if ($this->entryAlreadyExists($entry)) {
                throw new \Exception('SingleElement coordinates already set');
            }
            if ($entry->getRow() >= $n || $entry->getCol() >= $m) {
                throw new DimensionException('matrix element out of bounds');
            }
            $this->entries[] = $entry->getNumber();
            $this->rowIndices[] = $entry->getRow();
            $this->colIndices[] = $entry->getCol();
        }
    }

    public function __toString()
    {
        $string = "";

        // TODO

        return $string;
    }


    private function entryAlreadyExists(SingleElement $entry)
    {
        $rowMatches = array_keys($this->rowIndices, $entry->getRow());
        $colMatches = array_keys($this->colIndices, $entry->getCol());
        return sizeof(array_intersect($rowMatches, $colMatches)) > 0;
    }

    public function transpose()
    {
        $dimM = $this->dimM;
        $this->dimM = $this->dimN;
        $this->dimN = $dimM;

        $rowIndices = $this->rowIndices;
        $this->rowIndices = $this->colIndices;
        $this->colIndices = $rowIndices;

        return $this;
    }

    public function multiplyWithScalar(Number $number)
    {
        if ($number instanceof Zero) {
            $this->entries = [];
            $this->colIndices = [];
            $this->rowIndices = [];
        } else {
            parent::processMultiplyWithScalar($number);
        }
        return $this;
    }

    public function multiplyWithVector(VectorInterface $vector)
    {
        if ($vector->getDim() != $this->dimM) {
            throw new DimensionException('matrix and vector dimensions do not match');
        }
        $result = [];
        for ($i = 0; $i < $this->dimN; $i++) {
            $sum = Zero::getInstance();
            $rowElements = array_keys($this->rowIndices, $i);
            foreach ($rowElements as $j) {
                $sum = $sum->add($vector->get($i)->multiplyWith($this->entries[$j]));
            }
            $result[] = $sum;
        }

        return new Vector($result);
    }
}