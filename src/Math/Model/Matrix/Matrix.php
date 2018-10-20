<?php

namespace Math\Model\Matrix;


use Math\Exception\DimensionException;
use Math\Model\Number\Number;
use Math\Model\Number\Zero;
use Math\Model\Vector\Vector;
use Math\Model\Vector\VectorInterface;

class Matrix extends AbstractMatrix
{
    public function __construct(...$rows)
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

        $dimM = $this->dimN;
        $this->dimN = $this->dimM;
        $this->dimM = $dimM;

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

    public function multiplyWithVector(VectorInterface $vector)
    {
        $this->checkVectorDim($vector);
        $result = [];
        for ($i = 0; $i < $this->dimM; $i++) {
            $sum = Zero::getInstance();
            for ($j = 0; $j < $this->dimN; $j++) {
                $sum = $sum->add($vector->get($j+1)->multiplyWith_($this->entries[$i*$this->dimN + $j]));
            }
            $result[] = $sum;
        }

        return new Vector(...$result);
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
}