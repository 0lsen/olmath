<?php

namespace Math\Util;


use Math\Exception\DimensionException;
use Math\Model\Matrix\SparseInput\SingleElement;
use Math\Model\Number\Number;
use Math\Model\Number\RationalNumber;

class MatrixCreator
{
    public static function identity(int $m, int $n = null){

        $one = new RationalNumber(1);
        $entries = [];
        for ($i = 1; $i <= min($m, $n); $i++) {
            $entries[] = clone $one;
        }
        return self::diagonal($m, $n, ...$entries);
    }

    public static function diagonal(int $m, int $n = null, Number ...$numbers)
    {
        if (is_null($n)) $n = $m;
        if (sizeof($numbers) > min($m, $n)) {
            throw new DimensionException('cannot create ('.$m.','.$n.') diagonal matrix with '.sizeof($numbers).' elements.');
        }
        $entries = [];
        foreach ($numbers as $index => $number) {
            $entries[] = new SingleElement($index, $index, $number);
        }
        return new SparseMatrix($m, $n, ...$entries);
    }
}