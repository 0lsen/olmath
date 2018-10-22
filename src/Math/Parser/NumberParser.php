<?php

namespace Math\Parser;

use Math\Exception\DivisionByZeroException;
use Math\Exception\ParserException;
use Math\Model\Number\ComplexNumber;

class NumberParser
{
    private static $binaryOperators = '/*+-';
    private static $unaryOperators = '-';
    private static $validSymbols;

    private static $regexNumber = '((?:(?<![)0-9i])-)?(?:(?:[0-9]+)?\.)?(?:[0-9]+)|i)';
    private static $regexFormula;
    private static $regexBracket = '\([^()]+\)';

    /** @var ComplexNumber[] */
    private $numbers;

    private static $initialised = false;

    /**
     * NumberParser constructor.
     */
    public function __construct()
    {
        if (!self::$initialised) {
            self::init();
        }
    }

    private static function init()
    {
        self::$regexFormula = '^({\d+})(['. preg_quote(self::$binaryOperators, '#') .']({\d+}))*$';
        self::$validSymbols = '\(\)0-9i\.' . self::$binaryOperators . self::$unaryOperators;
        self::$initialised = true;
    }

    /**
     * @param $string
     * @return NumberResult[]
     */
    function evaluateFulltext($string)
    {
        $string = preg_replace(
            array('#\s+#', '#,#', '#\\xe3\\x97#', '#\\xe3\\xb7#', '#(\d)i#'),
            array('', '.', '*', '/', '$1*i'),
            $string
        );
        $results = [];
        if (preg_match_all('#['.self::$validSymbols.'][ '.self::$validSymbols.']+['.self::$validSymbols.']#', $string, $matches)) {
            foreach ($matches[0] as $match) {
                $this->numbers = [];
                $originalMatch = $match;
                $this->parseNumbers($match);
                if (preg_match('#^[\(]*\{1\}[\)]*$#', $match)) {
                    continue;
                }
                try {
                    $this->validate($match);
                    $results[] = new NumberResult($originalMatch, end($this->numbers));
                } catch (DivisionByZeroException $dbz) {
                    $results[] = new NumberResult($originalMatch, null, true);
                } catch (\Throwable $t) {}
            }
        }

        return $results;
    }

    private function parseNumbers(&$formula)
    {
        preg_match_all('#' . self::$regexNumber . '#', $formula, $matches);
        foreach ($matches[1] as $index => $match) {
            if (strlen($match)) {
                $number = $this->parseComplexNumber($match);
                $formula = preg_replace(
                    '#(?!{)' . preg_quote($matches[0][$index], '#') . '(?!})#',
                    '{' . $number . '}',
                    $formula,
                    1
                );
            }
        }
    }

    private function parseComplexNumber($string)
    {
        $r = 0; $i = 0;
        $neg = strpos($string, '-') === 0;
        if (strpos($string, 'i') !== false) {
            $str = substr($string, $neg ? 1 : 0, strlen($string) - ($neg ? 2 : 1));
            $i = (strlen($str) ? $str : 1) + 0;
        } else {
            $r = substr($string, $neg ? 1 : 0) + 0;
        }
        $this->numbers[] = new ComplexNumber($r, $i);
        return sizeof($this->numbers);
    }

    private function validate(&$formula)
    {
        $formula = preg_replace('#^\(([^\)]+)\)$#', '\\1', $formula);
        while(preg_match_all('#('.self::$regexBracket.')#', $formula, $matches)) {
            foreach ($matches[1] as $index => $match) {
                $this->validate($match);
                $formula = preg_replace('#'.preg_quote($matches[0][$index], '#').'#', $match, $formula, 1);
            }
        }

        foreach (str_split(self::$unaryOperators) as $operator) {
            while($this->resolveUnaryOperators($formula, $operator));
        }

        if (!preg_match('#'.self::$regexFormula.'#', $formula)) {
            throw new ParserException('string does not match formula regex: "'.$formula.'"');
        }

        foreach (str_split(self::$binaryOperators) as $operator) {
            while($this->resolveBinaryOperators($formula, $operator)) ;
        }
    }

    private function resolveBinaryOperators(&$formula, $operator)
    {
        try {
            $formula = preg_replace_callback(
                '#{(\d+)}(' . preg_quote($operator, '#') . '){(\d+)}#',
                function ($match) {return $this->resolveBinaryOperator($match);},
                $formula,
                1,
                $erfolg);
        } catch (DivisionByZeroException $dbz) {
            throw $dbz;
        } catch (\Throwable $t) {
            throw new ParserException('failed to resolve binary operator "'.$operator.'"');
        }
        return $erfolg;
    }

    private function resolveBinaryOperator($match)
    {
        switch ($match[2]) {
            case '+':
                $result = $this->numbers[$match[1]-1]->add_($this->numbers[$match[3]-1]);
                break;
            case '-':
                $result = $this->numbers[$match[1]-1]->subtract_($this->numbers[$match[3]-1]);
                break;
            case '*':
                $result = $this->numbers[$match[1]-1]->multiplyWith_($this->numbers[$match[3]-1]);
                break;
            case '/':
                $result = $this->numbers[$match[1]-1]->divideBy_($this->numbers[$match[3]-1]);
                break;
        }
        $this->numbers[] = $result;

        return '{' . sizeof($this->numbers) . '}';
    }

    private function resolveUnaryOperators(&$formula, $operator)
    {
        try {
            $formula = preg_replace_callback(
                '#(?<!})(' . preg_quote($operator, '#') . '){(\d+)}#',
                function ($match) {return $this->resolveUnaryOperator($match);},
                $formula,
                1,
                $erfolg);
        } catch (\Throwable $t) {
            throw new ParserException('failed to resolve unary operator "'.$operator.'"');
        }
        return $erfolg;
    }

    private function resolveUnaryOperator($match)
    {
        switch ($match[1]) {
            case '-':
                $result = $this->numbers[$match[2]-1]->negative_();
                break;
        }
        $this->numbers[] = $result;

        return '{' . sizeof($this->numbers) . '}';
    }
}