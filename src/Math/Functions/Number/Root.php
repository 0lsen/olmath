<?php

namespace Math\Functions\Number;

use Math\Exception\UnknownOperandException;

class Root
{
    // TODO: use relative accuarcy
    public static $decimalPlacesAccuracy = 8;

    public static function nthRoot($number, int $n) {
        if (!is_numeric($number)) throw new UnknownOperandException('root of non numeric value');
        if ($number < 0) throw new UnknownOperandException('root of negative number');
        if ($n < 0) throw new UnknownOperandException('negative root of number');
        if ($number == 0) return 0;

        $x = 1; $xNew = 1;
        do {
            $x = $xNew;
            $xNew = (1/$n) * (($n-1)*$x + $number/pow($x, $n-1));
        }
        while (round($x-$xNew, self::$decimalPlacesAccuracy) != 0);

        return $xNew;
    }
}