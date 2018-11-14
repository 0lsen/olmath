<?php

namespace Math\Model\Matrix;


use Math\Exception\DimensionException;
use Math\Model\Matrix\SparseInput\SingleElement;
use Math\Model\Number\Number;
use Math\Model\Number\Zero;
use Math\Model\Vector\SparseVector;
use Math\Model\Vector\Vector;
use Math\Model\Vector\VectorInterface;

class SparseMatrix extends AbstractMatrix
{
    /** @var int[] */
    private $rowIndices = [];
    /** @var int[] */
    private $colIndices = [];

    public function __construct(int $rows, int $cols, SingleElement ...$entries)
    {
        if ($rows < 1 || $cols < 1) {
            throw new DimensionException('invalid matrix size ('.$rows.','.$cols.')');
        }

        $this->dimM = $rows;
        $this->dimN = $cols;

        foreach ($entries as $entry) {
            if (!is_null($this->getEntryIndex($entry->getRow(), $entry->getCol()))) {
                throw new \Exception('SingleElement coordinates ('.($entry->getRow()+1).':'.($entry->getCol()+1).') are already occupied.');
            }
            if ($entry->getRow() >= $rows || $entry->getCol() >= $cols) {
                throw new DimensionException('matrix element ('.($entry->getRow()+1).','.($entry->getCol()+1).') is out of bounds ('.$this->dimM.','.$this->dimN.').');
            }
            if ($entry->getNumber() instanceof Zero) continue;
            $this->entries[] = $entry->getNumber();
            $this->rowIndices[] = $entry->getRow();
            $this->colIndices[] = $entry->getCol();
        }
    }

    public function __toString()
    {
        if (!$this->entries) return "[ ]";
        $string = "";
        $longestEntries = array_fill(0, $this->dimN, 0);
        for ($i = 0; $i < $this->dimN; $i++) {
            foreach (array_keys($this->colIndices, $i) as $index) {
                if ($this->entries[$index]->value()) {
                    $strLen = strlen((string) $this->entries[$index]) + strlen((string) ($i+1)) + strlen((string) ($this->rowIndices[$index]+1));
                    if ($longestEntries[$i] < $strLen)
                        $longestEntries[$i] = $strLen;
                }
            }
        }
        for ($i = 0; $i < $this->dimM; $i++) {
            if (!array_keys($this->rowIndices, $i)) continue;
            if ($string) $string .= "\n";
            $string .= "[ ";
            for ($j = 0; $j < $this->dimN; $j++) {
                if (!$longestEntries[$j]) continue;
                if ($j) $string .= "   ";
                $entry = $this->getEntryIndex($i, $j);
                if (is_null($entry) || !$this->entries[$entry]->value()) {
                    $string .= str_repeat(" ", $longestEntries[$j]+3);
                } else {
                    $str = (string) $this->entries[$entry];
                    $spaces = $longestEntries[$j]-strlen($str)-strlen((string) ($i+1))-strlen((string) ($j+1));
                    $string .= ($i+1) . "," . ($j+1) . ": ";
                    if ($spaces) $string .= str_repeat(" ", $spaces);
                    $string .= $str;
                }
            }
            $string .= " ]";
        }
        return $string;
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

    public function multiplyWithVector(VectorInterface $vector, bool $transposed = false)
    {
        $this->checkVectorDim($vector, !$transposed);
        $result = [];
        for ($i = 0; $i < ($transposed ? $this->dimN : $this->dimM); $i++) {
            $sum = Zero::getInstance();
            $rowElements = array_keys($transposed ? $this->colIndices : $this->rowIndices, $i);
            foreach ($rowElements as $j) {
                $sum = $sum->add($vector->get($transposed ? $this->rowIndices[$j]+1 : $this->colIndices[$j]+1)->multiplyWith_($this->entries[$j]));
            }
            $result[] = $sum;
        }

        return new Vector(...$result);
    }

    public function addMatrix(MatrixInterface $matrix)
    {
        $this->checkMatrixDims($matrix);
        for ($i = 1; $i <= $this->dimM; $i++) {
            for ($j = 1; $j <= $this->dimN; $j++) {
                $toAdd = $matrix->get_($i, $j);
                if ($toAdd->value() != 0) {
                    $current = $this->getEntryIndex($i-1, $j-1);
                    if (is_null($current)) {
                        $this->rowIndices[] = $i-1;
                        $this->colIndices[] = $j-1;
                        $this->entries[] = $toAdd;
                    } else {
                        $this->entries[$current] = $this->entries[$current]->add($toAdd);
                    }
                }
            }
        }
        return $this;
    }

    public function get(int $i, int $j)
    {
        $this->checkDims($i, $j);
        $entry = $this->getEntryIndex($i-1, $j-1);
        return is_null($entry) ? Zero::getInstance() : $this->entries[$entry];
    }

    private function getEntryIndex(int $i, int $j)
    {
        $match = array_intersect(
            array_keys($this->rowIndices, $i),
            array_keys($this->colIndices, $j)
        );
        return $match ? reset($match) : null;
    }

    public function getRow(int $i)
    {
        $entries = [];
        foreach (array_keys($this->rowIndices, $i-1) as $index) {
            $entries[$this->colIndices[$index]] = $this->entries[$index];
        }
        return new SparseVector($this->dimM, $entries);
    }

    public function getCol(int $i)
    {
        $entries = [];
        foreach (array_keys($this->colIndices, $i-1) as $index) {
            $entries[$this->rowIndices[$index]] = $this->entries[$index];
        }
        return new SparseVector($this->dimN, $entries);
    }

    public function set(int $i, int $j, Number $number)
    {
        $this->checkDims($i, $j);
        $entry = $this->getEntryIndex(--$i, --$j);
        if (is_null($entry)) {
            $this->rowIndices[] = $i;
            $this->colIndices[] = $j;
            $this->entries[] = $number;
        } else {
            $this->entries[$entry] = $number;
        }
        return $this;
    }

    private function unset(int $i, int $j)
    {
        $entry = $this->getEntryIndex($i, $j);
        if (!is_null($entry)) {
            $this->removeEntry($entry);
        }
    }

    private function removeEntry($index)
    {
        array_splice($this->rowIndices, $index, 1);
        array_splice($this->colIndices, $index, 1);
        array_splice($this->entries, $index, 1);
    }

    public function setRow(int $i, VectorInterface $vector)
    {
        $this->checkVectorDim($vector);
        for ($j = 0; $j < $this->dimN; $j++) {
            $entry = $vector->get($j+1);
            if ($entry->value() == 0) {
                $this->unset($i, $j+1);
            } else {
                $this->set($i, $j+1, $entry);
            }
        }
        return $this;
    }

    public function setCol(int $i, VectorInterface $vector)
    {
        $this->checkVectorDim($vector, false);
        for ($j = 0; $j < $this->dimM; $j++) {
            $entry = $vector->get($j+1);
            if ($entry->value() == 0) {
                $this->unset($j+1, $i);
            } else {
                $this->set($j+1, $i, $entry);
            }
        }
        return $this;
    }

    public function appendRow(VectorInterface $vector)
    {
        $this->checkVectorDim($vector);
        for ($i = 0; $i < $this->dimN; $i++) {
            $number = $vector->get($i+1);
            if ($number->value()) {
                $this->entries[] = $number;
                $this->rowIndices[] = $this->dimM;
                $this->colIndices[] = $i;
            }
        }
        $this->dimM++;
        return $this;
    }

    public function appendCol(VectorInterface $vector)
    {
        $this->checkVectorDim($vector, false);
        for ($i = 0; $i < $this->dimM; $i++) {
            $number = $vector->get($i+1);
            if ($number->value()) {
                $this->entries[] = $number;
                $this->rowIndices[] = $i;
                $this->colIndices[] = $this->dimN;
            }
        }
        $this->dimN++;
        return $this;
    }

    public function removeRow(int $i)
    {
        if ($i < 1 || $i >= $this->dimM) {
            throw new DimensionException('invalid row '.$i.' in ('.$this->dimM.','.$this->dimN.') matrix.');
        }
        $indices = array_keys($this->rowIndices, $i-1);
        foreach ($indices as $index) {
            $this->removeEntry($index);
        }
        foreach ($this->rowIndices as &$index) {
            if ($index >= $i)
                $index--;
        }
        $this->dimM--;
        return $this;
    }

    public function removeCol(int $i)
    {
        if ($i < 1 || $i >= $this->dimN) {
            throw new DimensionException('invalid col '.$i.' in ('.$this->dimM.','.$this->dimN.') matrix.');
        }
        $indices = array_keys($this->colIndices, $i-1);
        foreach ($indices as $index) {
            $this->removeEntry($index);
        }
        foreach ($this->colIndices as &$index) {
            if ($index >= $i)
                $index--;
        }
        $this->dimN--;
        return $this;
    }

    public function trim(int $m, int $n)
    {
        if ($m < $this->dimM) {
            for ($i = sizeof($this->rowIndices); $i > 0; $i-- ) {
                if ($this->rowIndices[$i-1] >= $m) {
                    $this->removeEntry($i-1);
                }
            }
        }
        if ($n < $this->dimN) {
            for ($i = sizeof($this->colIndices); $i > 0; $i-- ) {
                if ($this->colIndices[$i-1]>= $n) {
                    $this->removeEntry($i-1);
                }
            }
        }
        $this->dimM = $m;
        $this->dimN = $n;
        return $this;
    }
}