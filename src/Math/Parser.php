<?php

namespace Math;

use Math\Number\Exception\DivisionByZeroException;
use Math\Number\Model\ComplexNumber;

abstract class Parser
{
    private static $operators = '\+\-\/*';
    private static $operatorsToReplace = ',\\xe3\\x97\\xe3\\xb7';

    private static $regexNumber = '((?:(?<![)0-9i])-)?(?:(?:[0-9]+)?\.)?(?:[0-9]+)?i?)';
    private static $regexFormula;
    private static $regexBracket = '\([^()]+\)';

    /** @var ComplexNumber[] */
    private static $numbers;

    static function init()
    {
        self::$regexFormula = '^({\d+})(['. self::$operators .']({\d+}))*$';
    }

    /**
     * @param $string
     * @return Result[]
     */
    static function evaluate($string)
    {
        $results = [];
        if (preg_match_all('#[ 0-9i\.' . self::$operators . self::$operatorsToReplace . '\(\)]+#', $string, $matches)) {
            foreach ($matches[0] as $match) {
                self::$numbers = [];
                $match = preg_replace(
                    array('#\s+#', '#,#', '#\\xe3\\x97#', '#\\xe3\\xb7#'),
                    array('', '.', '*', '/'),
                    $match
                );
                $originalMatch = $match;
                self::parseNumbers($match);
                if (preg_match('#^[\(]*\{1\}[\)]*$#', $match)) {
                    continue;
                }
                try {
                    if (self::validate($match)) {
                        $results[] = new Result($originalMatch, end(self::$numbers));
                    }
                } catch (DivisionByZeroException $dbz) {
                    $results[] = new Result($originalMatch, null, true);
                } catch (\Throwable $t) {}
            }
        }

        return $results;
    }

    private static function parseNumbers(&$formula)
    {
        preg_match_all('#' . self::$regexNumber . '#', $formula, $matches);
        foreach ($matches[1] as $index => $match) {
            if (strlen($match)) {
                $number = self::parseComplexNumber($match);
                $formula = preg_replace(
                    '#(?!{)' . preg_quote($matches[0][$index], '#') . '(?!})#',
                    '{' . $number . '}',
                    $formula,
                    1
                );
            }
        }
    }

    private static function parseComplexNumber($string)
    {
        $r = 0; $i = 0;
        $neg = strpos($string, '-') === 0;
        if (strpos($string, 'i') !== false) {
            $str = substr($string, $neg ? 1 : 0, strlen($string) - ($neg ? 2 : 1));
            $i = (strlen($str) ? $str : 1) + 0;
        } else {
            $r = substr($string, $neg ? 1 : 0) + 0;
        }
        self::$numbers[] = new ComplexNumber($r, $i);
        return sizeof(self::$numbers);
    }

    private static function validate(&$formula)
    {
        $formula = preg_replace('#^\(([^\)]+)\)$#', '\\1', $formula);
        while(preg_match_all('#('.self::$regexBracket.')#', $formula, $matches)) {
            foreach ($matches[1] as $index => $match) {
                if (!self::validate($match)) {
                    return false;
                }
                $formula = preg_replace('#'.preg_quote($matches[0][$index], '#').'#', $match, $formula, 1);
            }
        }
        if (!preg_match('#'.self::$regexFormula.'#', $formula)) {
            return false;
        }
        while(self::resolveOperators($formula, '*/'));
        while(self::resolveOperators($formula, '+\-'));
        return true;
    }

    private static function resolveOperators(&$formula, $operators)
    {
        $formula = preg_replace_callback(
            '#{(\d+)}([' . $operators . ']){(\d+)}#',
            function ($match) {return self::resolveOperator($match);},
            $formula,
            1,
            $erfolg);
        return $erfolg;
    }

    private static function resolveOperator($match)
    {
        switch ($match[2]) {
            case '+':
                $result = self::$numbers[$match[1]-1]->add_(self::$numbers[$match[3]-1]);
                break;
            case '-':
                $result = self::$numbers[$match[1]-1]->subtract_(self::$numbers[$match[3]-1]);
                break;
            case '*':
                $result = self::$numbers[$match[1]-1]->multiplyWith_(self::$numbers[$match[3]-1]);
                break;
            case '/':
                $result = self::$numbers[$match[1]-1]->divideBy_(self::$numbers[$match[3]-1]);
                break;
        }
        self::$numbers[] = $result;

        return '{' . sizeof(self::$numbers) . '}';
    }
}