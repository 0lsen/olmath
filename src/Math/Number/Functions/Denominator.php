<?php

namespace Math\Number\Functions;

class Denominator
{
    public static function GCD(int $a, int $b) {
        if (!$a && !$b) {
            return 0;
        } else {
            return self::shortGCD($a, $b);
        }
    }

    private static function shortGCD(int $a, int $b) {
        return $b ? self::GCD($b, $a % $b) : $a;
    }

    public static function LCM(int $a, int $b) {
        if (!$a && !$b) {
            return 0;
        }else {
            return self::shortLCM($a, $b);
        }

    }

    private static function shortLCM(int $a, int $b) {
        $c = self::shortGCD($a, $b);
        return ($a * $b) / $c;
    }
}