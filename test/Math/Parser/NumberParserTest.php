<?php

use Math\Parser\NumberParser;
use PHPUnit\Framework\TestCase;

class NumberParserTest extends TestCase
{
    function setUp()
    {
        NumberParser::init();
        parent::setUp();
    }

    function testOperators()
    {
        $string = '1 + 2';
        $results = NumberParser::evaluate($string);
        $this->assertEquals('3', (string) $results[0]->getResult());

        $string = '3 - 1';
        $results = NumberParser::evaluate($string);
        $this->assertEquals('2', (string) $results[0]->getResult());

        $string = '3 * 2';
        $results = NumberParser::evaluate($string);
        $this->assertEquals('6', (string) $results[0]->getResult());

        $string = '6 / 2';
        $results = NumberParser::evaluate($string);
        $this->assertEquals('3', (string) $results[0]->getResult());
    }

    function testDivisionByZero()
    {
        $string = '1 / 0';
        $results = NumberParser::evaluate($string);
        $this->assertTrue($results[0]->isDbz());
    }

    function testTrivialInput()
    {
        $string = '((-1))';
        $results = NumberParser::evaluate($string);
        $this->assertEquals(0, sizeof($results));

        $string = '3i';
        $results = NumberParser::evaluate($string);
        $this->assertEquals(0, sizeof($results));
    }

    function testValidBrackets()
    {
        $string = '(1+1)';
        $results = NumberParser::evaluate($string);
        $this->assertEquals('2', (string) $results[0]->getResult());

        $string = '1 + (2 + 3)';
        $results = NumberParser::evaluate($string);
        $this->assertEquals('6', (string) $results[0]->getResult());

        $string = '(2+4) / (6*(2+3))';
        $results = NumberParser::evaluate($string);
        $this->assertEquals('1/5', (string) $results[0]->getResult());
    }

    function testInvalidBrackets()
    {
        $string = '((1+1)';
        $results = NumberParser::evaluate($string);
        $this->assertEquals(0, sizeof($results));

        $string = '(1+()+1)';
        $results = NumberParser::evaluate($string);
        $this->assertEquals(0, sizeof($results));
    }

    function testOrderOfOperations()
    {
        $string = '4/2 + 1';
        $results = NumberParser::evaluate($string);
        $this->assertEquals('3', (string) $results[0]->getResult());

        $string = '1 + 4/2';
        $results = NumberParser::evaluate($string);
        $this->assertEquals('3', (string) $results[0]->getResult());
    }

    function testRealNumbers()
    {
        $string = '3.4 + 2.1';
        $results = NumberParser::evaluate($string);
        $this->assertEquals('5.5', (string) $results[0]->getResult());

        $string = '3 + 2.1';
        $results = NumberParser::evaluate($string);
        $this->assertEquals('5.1', (string) $results[0]->getResult());
    }

    function testRationalNumbers()
    {
        $string = '5/3 + 1/6';
        $results = NumberParser::evaluate($string);
        $this->assertEquals('1 5/6', (string) $results[0]->getResult());
    }

    function testComplexNumbers()
    {
        $string = '1+2i';
        $results = NumberParser::evaluate($string);
        $this->assertEquals('1 + 2 i', (string) $results[0]->getResult());

        $string = '(1+2i)*i';
        $results = NumberParser::evaluate($string);
        $this->assertEquals('-2 + i', (string) $results[0]->getResult());
    }

    function testReplacements()
    {
        $string = '1,1 + 1';
        $results = NumberParser::evaluate($string);
        $this->assertEquals('2.1', (string) $results[0]->getResult());

        $string = '1/3 i';
        $results = NumberParser::evaluate($string);
        $this->assertEquals('1/3 i', (string) $results[0]->getResult());
    }
}
