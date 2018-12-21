<?php

namespace Math\Parser;

use Math\Exception\DivisionByZeroException;
use Math\Exception\ParserException;
use Math\Model\Number\ComplexNumber;
use Math\Model\Number\Number;
use Math\Model\Number\RationalNumber;
use Math\Model\Number\RealNumber;
use Math\Model\Number\Zero;

class FormulaParser
{
    private static $binaryOperators = '/*+-';
    private static $unaryOperators = '-';
    private static $whitespaces = '\t ';
    private static $validSymbols;

    private static $regexNumber;
    private static $regexFormula;
    private static $regexBracket = '\([^()]+\)';
    private static $regexInline;
    private static $regexLine;

    private $replacements = [
        '#[\t ]+#' => '',
        '#(\d)i#' => '$1*i',
        '#(\w)\(#' => '$1*(',
        '#\)(\w)#' => ')*$1',
    ];

    /** @var Number[] */
    private $numbers = [];
    /** @var int */
    private $numberIndex;

    private static $initialised = false;

    /**
     * FormulaParser constructor.
     */
    public function __construct(string $decimalPoint = '.' , string $groupSeparator = ',')
    {
        if (!self::$initialised) {
            self::init();
        }
        $this->replacements['#'.preg_quote($decimalPoint, '#').'#'] = '.';
        $this->replacements['#'.preg_quote($groupSeparator, '#').'#'] = '';
    }

    private static function init()
    {
        self::$regexNumber = '(?<=^|[()'.preg_quote(self::$binaryOperators, '#').'])(-?(?:(?:[0-9]+)?\.)?(?:[0-9]+)|i)';
        self::$regexFormula = '^({\w+})(['. preg_quote(self::$binaryOperators, '#') .']({\w+}))*$';
        self::$validSymbols = '\(\)0-9i\.' . self::$binaryOperators . self::$unaryOperators;

        self::$regexInline = '['.self::$validSymbols.']['.self::$whitespaces.self::$validSymbols.']+['.self::$validSymbols.']';
        self::$regexLine = '['.self::$whitespaces.']*(?:([a-zA-Z]\w*)['.self::$whitespaces.']*\=['.self::$whitespaces.']*|)(['.self::$whitespaces.'a-zA-Z'.self::$validSymbols.']+)';

        self::$initialised = true;
    }

    /**
     * @param $string
     * @return FormulaResult
     */
    function evaluateFulltext($string)
    {
        $string = preg_replace(
            array('#\\xe3\\x97#', '#\\xe3\\xb7#'),
            array('*', '/'),
            $string
        );
        $result = new FormulaResult();
        if (preg_match_all('#^'.self::$regexLine.'$#m', $string, $matches)) {
            foreach ($matches[2] as $index =>  $match) {
                try {
                    $this->evaluateMultiLine($match, $matches[1][$index], $result);
                } catch (\Throwable $t) {}
            }
        }
        if (!$result->getEntries() && preg_match_all('#'.self::$regexInline.'#', $string, $matches)) {
            foreach ($matches[0] as $match) {
                $this->evaluateInline($match, $result);
            }
        }

        return $result;
    }

    function evaluate($string)
    {
        $result = new FormulaResult();
        $this->reset();
        if (preg_match_all('#^'.self::$regexLine.'$#m', $string, $matches) == substr_count($string, "\n")+1) {
            foreach ($matches[2] as $index => $match) {
                $this->evaluateMultiLine($match, $matches[1][$index], $result);
            }
        } else {
            throw new ParserException('Failed formula regex check.');
        }

        return $result;
    }

    private function evaluateInline($match, FormulaResult $result)
    {
        $this->numbers = [];
        $originalMatch = $match;
        foreach ($this->replacements as $needle => $replace) {
            $match = preg_replace($needle, $replace, $match);
        }
        $this->parseNumbers($match);
        if (preg_match('#^[\(]*\{1\}[\)]*$#', $match)) {
            return;
        }
        try {
            $this->validate($match);
            $result->addResult(new FormulaResultEntry($originalMatch, end($this->numbers)));
        } catch (DivisionByZeroException $dbz) {
            $result->addResult(new FormulaResultEntry($originalMatch, null, true));
        } catch (\Throwable $t) {}
    }

    private function evaluateMultiLine($match, $variable, FormulaResult $result)
    {
        $originalMatch = preg_replace(['#^['.self::$whitespaces.']*#', '#['.self::$whitespaces.']*$#'], ['', ''], $match);
        foreach ($this->replacements as $needle => $replace) {
            $match = preg_replace($needle, $replace, $match);
        }
        $this->parseVariables($match);
        $this->parseNumbers($match);
        try {
            $this->validate($match);
            $entry = new FormulaResultEntry($originalMatch, $this->getNumber($match));
        } catch (DivisionByZeroException $dbz) {
            $entry = new FormulaResultEntry($originalMatch, null, true);
        } catch (\Throwable $t) {
            throw $t;
        }
        if ($variable) {
            if ($variable == 'i') {
                throw new ParserException('Cannot overwrite variable "i"');
            }
            $entry->setVariable($variable);
            $this->numbers[$variable] = clone $this->getNumber($match);
        }
        $result->addResult($entry, $variable ? $variable : null);
    }

    private function reset()
    {
        $this->numbers = [];
        $this->numberIndex = 0;
    }

    private function addNumber(Number $number)
    {
        $this->numbers[++$this->numberIndex] = $number;
        return $this->numberIndex;
    }

    private function getNumber(string $string)
    {
        preg_match('#^{(\w+)}$#', $string, $match);
        if (isset($match[1])) {
            return clone $this->numbers[$match[1]];
        } else {
            throw new ParserException('Could not get number from expression "'.$string.'"');
        }
    }

    private function parseNumbers(&$formula)
    {
        $formula = preg_replace_callback(
            '#' . self::$regexNumber . '#',
            function ($match) {return $this->parseNumber($match[0]);},
            $formula
        );
    }

    private function parseNumber($string)
    {
        if ($string == 'i') {
            $this->addNumber(new ComplexNumber(0, 1));
        } elseif (strpos($string, '.') !== false) {
            $this->addNumber(new RealNumber($string));
        } elseif ($string == 0) {
            $this->addNumber(Zero::getInstance());
        } else {
            $this->addNumber(new RationalNumber(
                abs($string),
                1,
                $string <=> 0
            ));
        }

        return '{' . $this->numberIndex . '}';
    }

    private function parseVariables(&$formula)
    {
        $formula = preg_replace_callback(
            '#[a-zA-Z]\w*#',
            function ($match) {return $this->parseVariable($match[0]);},
            $formula
        );
    }

    private function parseVariable($string)
    {
        if ($string == 'i') return 'i';
        if (isset($this->numbers[$string])) {
            return '{' . $string . '}';
        } else {
            throw new ParserException('Unknown variable "'.$string.'"');
        }
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
                '#{(\w+)}(' . preg_quote($operator, '#') . '){(\w+)}#',
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
                $result = $this->numbers[$match[1]]->add_($this->numbers[$match[3]]);
                break;
            case '-':
                $result = $this->numbers[$match[1]]->subtract_($this->numbers[$match[3]]);
                break;
            case '*':
                $result = $this->numbers[$match[1]]->multiplyWith_($this->numbers[$match[3]]);
                break;
            case '/':
                $result = $this->numbers[$match[1]]->divideBy_($this->numbers[$match[3]]);
                break;
        }

        return '{' . $this->addNumber($result) . '}';
    }

    private function resolveUnaryOperators(&$formula, $operator)
    {
        try {
            $formula = preg_replace_callback(
                '#(?<!})(' . preg_quote($operator, '#') . '){(\w+)}#',
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
                $result = $this->numbers[$match[2]]->negative_();
                break;
        }

        return '{' . $this->addNumber($result) . '}';
    }
}