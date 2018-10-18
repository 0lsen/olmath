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
        $this->dimN = sizeof($rows);
        $this->dimM = sizeof($rows[0]);

        foreach ($rows as $row) {
            if (sizeof($row) != $this->dimM) {
                throw new DimensionException('row dimensions do not match');
            }
            foreach ($row as $entry) {
                $this->entries[] = $entry;
            }
        }
    }

    public function __toString()
    {
        $string = "";
        $longestEntries = array_fill(0, $this->dimM, 0);
        for ($i = 0; $i < $this->dimM; $i++) {
            for ($j = 0; $j < $this->dimN; $j++) {
                $strLen = strlen((string) $this->entries[$j*$this->dimM + $i]);
                if ($longestEntries[$i] < $strLen)
                    $longestEntries[$i] = $strLen;
            }
        }
        for ($j = 0; $j < $this->dimN; $j++) {
            if ($j) $string .= "\n";
            $string .= "[ ";
            for ($i = 0; $i < $this->dimM; $i++) {
                if ($i) $string .= " | ";
                $str = (string) $this->entries[$j*$this->dimM + $i];
                $strLen = strlen($str);
                $string .= str_repeat(" ", $longestEntries[$i]-$strLen) . $str;
            }
            $string .= " ]";
        }
        return $string;
    }

    public function transpose()
    {
        $newEntries = [];
        foreach ($this->entries as $index => $entry) {
            $newEntries[$index%$this->dimM * $this->dimN + floor($index/$this->dimM)] = $entry;
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
            $this->entries = array_fill(0, $this->dimM*$this->dimN, Zero::getInstance());
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
            for ($j = 0; $j < $this->dimM; $j++) {
                $sum = $sum->add($vector->get($i)->multiplyWith($this->entries[$i*$this->dimM + $j]));
            }
            $result[] = $sum;
        }

        return new Vector($result);
    }
}