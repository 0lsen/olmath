<?php

namespace Math\Functions\LinearAlgebra;


use Math\Model\Matrix\MatrixInterface;
use Math\Model\Number\Zero;
use Math\Model\Vector\VectorInterface;

class LinearSystemSolver
{
    public static function gaussElimination(MatrixInterface $matrix, VectorInterface $vector)
    {
        $getPivot = function(VectorInterface $row) {
            $max = Zero::getInstance();
            $maxIndex = 0;
            foreach ($row as $index => $number) {
                if ($max->absoluteValue() < $number->absoluteValue()) {
                    $max = clone $number;
                    $maxIndex = $index;
                }
            }
            return $maxIndex+1;
        };
        list($m, $n) = $matrix->getDims();
        $h = 1; $k = 1;
        while ($h <= $m && $k <= $n) {
            $iMax = $getPivot($matrix->getCol($k));
            if ($matrix->get($iMax, $k)->value() == 0) {
                $k++;
            } else {
                if ($h != $iMax) {
                    $swapRow = $matrix->getRow_($h);
                    $matrix->setRow($h, $matrix->getRow($iMax));
                    $matrix->setRow($iMax, $swapRow);
                }
                for ($i = $h+1; $h <= $m; $h++) {
                    $f = $matrix->get($i, $k)->divideBy_($matrix->get($h, $k));
                    $matrix->set($i, $k, Zero::getInstance());
                    for ($j = $k+1; $j < $n; $j++) {
                        $matrix->set($i, $j, $matrix->get($i,$j)->subtract_($matrix->get($h, $j)->multiplyWith_($f)));
                    }
                }
                $h++; $k++;
            }
        }
    }

}