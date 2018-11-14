<?php

namespace Math\Model\Matrix;


use Math\Exception\DimensionException;
use Math\Model\Number\Number;
use Math\Model\Number\Zero;
use Math\Model\Vector\Vector;
use Math\Model\Vector\VectorInterface;

class Matrix extends AbstractMatrix
{
    public function __construct(array ...$rows)
    {
        $this->dimM = sizeof($rows);
        $this->dimN = sizeof($rows[0]);

        foreach ($rows as $row) {
            if (sizeof($row) != $this->dimN) {
                throw new DimensionException('row dimensions do not match. '.$this->dimN.' expected, '.sizeof($row).' found.');
            }
            foreach ($row as $entry) {
                $this->entries[] = $entry;
            }
        }
    }

    public function __toString()
    {
        $string = "";
        $longestEntries = array_fill(0, $this->dimN, 0);
        for ($i = 0; $i < $this->dimN; $i++) {
            for ($j = 0; $j < $this->dimM; $j++) {
                $strLen = strlen((string) $this->entries[$j*$this->dimN + $i]);
                if ($longestEntries[$i] < $strLen)
                    $longestEntries[$i] = $strLen;
            }
        }
        for ($i = 0; $i < $this->dimM; $i++) {
            if ($i) $string .= "\n";
            $string .= "[ ";
            for ($j = 0; $j < $this->dimN; $j++) {
                if ($j) $string .= " | ";
                $str = (string) $this->entries[$i*$this->dimN + $j];
                $strLen = strlen($str);
                $string .= str_repeat(" ", $longestEntries[$j]-$strLen) . $str;
            }
            $string .= " ]";
        }
        return $string;
    }

    public function transpose()
    {
        $newEntries = [];
        foreach ($this->entries as $index => $entry) {
            $newEntries[$index%$this->dimN * $this->dimM + floor($index/$this->dimN)] = $entry;
        }
        $this->entries = $newEntries;

        $dimM = $this->dimM;
        $this->dimM = $this->dimN;
        $this->dimN = $dimM;

        return $this;
    }

    public function multiplyWithScalar(Number $number)
    {
        if ($number instanceof Zero) {
            $this->entries = array_fill(0, $this->dimN*$this->dimM, Zero::getInstance());
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
            for ($j = 0; $j < ($transposed ? $this->dimM : $this->dimN); $j++) {
                $sum = $sum->add($vector->get($j+1)->multiplyWith_($this->entries[$transposed ? $j*$this->dimN + $i : $i*$this->dimN + $j]));
            }
            $result[] = $sum;
        }

        return new Vector(...$result);
    }

    public function addMatrix(MatrixInterface $matrix)
    {
        $this->checkMatrixDims($matrix);
        foreach ($this->entries as $index => &$entry) {
            $entry = $entry->add($matrix->get(floor($index/$this->dimN)+1, $index%$this->dimN+1));
        }
        return $this;
    }

    public function get(int $i, int $j)
    {
        $this->checkDims($i, $j);
        return $this->entries[--$i*$this->dimN + --$j];
    }

    public function getRow(int $i)
    {
        $entries = [];
        for ($j = 0; $j < $this->dimN; $j++) {
            $entries[] = $this->get($i, $j+1);
        }
        return new Vector(...$entries);
    }

    public function getCol(int $i)
    {
        $entries = [];
        for ($j = 0; $j < $this->dimM; $j++) {
            $entries[] = $this->get($j+1, $i);
        }
        return new Vector(...$entries);
    }

    public function set(int $i, int $j, Number $number)
    {
        $this->checkDims($i, $j);
        $this->entries[--$i*$this->dimN + --$j] = $number;
        return $this;
    }

    public function setRow(int $i, VectorInterface $vector)
    {
        $this->checkVectorDim($vector);
        for ($j = 0; $j < $this->dimN; $j++) {
            $this->set($i, $j+1, $vector->get($j+1));
        }
        return $this;
    }

    public function setCol(int $i, VectorInterface $vector)
    {
        $this->checkVectorDim($vector, false);
        for ($j = 0; $j < $this->dimM; $j++) {
            $this->set($j+1, $i, $vector->get($j+1));
        }
        return $this;
    }

    public function appendRow(VectorInterface $vector)
    {
        $this->checkVectorDim($vector);
        for ($i = 0; $i < $this->dimN; $i++) {
            $this->entries[] = $vector->get($i+1);
        }
        $this->dimM++;
        return $this;
    }

    public function appendCol(VectorInterface $vector)
    {
        $this->checkVectorDim($vector, false);
        for ($i = 0; $i < $this->dimM; $i++) {
            array_splice($this->entries, ($i+1) *( $this->dimN+1) - 1, 0, [$vector->get($i+1)]);
        }
        $this->dimN++;
        return $this;
    }

    public function removeRow(int $i)
    {
        if ($i < 1 || $i >= $this->dimM) {
            throw new DimensionException('invalid row '.$i.' in ('.$this->dimM.','.$this->dimN.') matrix.');
        }
        array_splice($this->entries, ($i-1)*$this->dimN, $this->dimN);
        $this->dimM--;
        return $this;
    }

    public function removeCol(int $i)
    {
        if ($i < 1 || $i >= $this->dimN) {
            throw new DimensionException('invalid col '.$i.' in ('.$this->dimM.','.$this->dimN.') matrix.');
        }
        for ($j = $this->dimM; $j > 0 ; $j--) {
            array_splice($this->entries, ($j-1)*$this->dimN + ($i-1), 1);
        }
        $this->dimN--;
        return $this;
    }

    public function trim(int $m, int $n = null)
    {
        if (is_null($m)) $m = $this->dimM;
        if (is_null($n)) $n = $this->dimN;
        if ($m < 1 || $n < 1) {
            throw new DimensionException('can not trim matrix to dimensions ('.$m.','.$n.')');
        }
        if ($n != $this->dimN) {
            $enlarge = $n > $this->dimN;
            $diff = abs($this->dimN-$n);
            for ($i = 0; $i < $this->dimM; $i++) {
                array_splice(
                    $this->entries,
                    $enlarge ? ($i+1)*$n-1 : ($i+1)*$n,
                    $enlarge ? 0 : $diff,
                    $enlarge ? array_fill(0, $diff, Zero::getInstance()) : null
                );
            }
        }
        switch ($m <=> $this->dimM) {
            case 0:
                break;
            case 1:
                $this->entries = array_pad($this->entries, $m*$n, Zero::getInstance());
                break;
            case -1:
                $this->entries = array_slice($this->entries, ($m-1)*$n);
                break;
        }
        $this->dimM = $m;
        $this->dimN = $n;
        return $this;
    }
}