<?php

use Math\Parser;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    function setUp()
    {
        Parser::init();
        parent::setUp();
    }

    function testOperators()
    {
        $string = '1 + 2';
        $results = Parser::evaluate($string);
        $this->assertEquals('3', (string) $results[0]->getResult());

        $string = '3 - 1';
        $results = Parser::evaluate($string);
        $this->assertEquals('2', (string) $results[0]->getResult());

        $string = '3 * 2';
        $results = Parser::evaluate($string);
        $this->assertEquals('6', (string) $results[0]->getResult());

        $string = '6 / 2';
        $results = Parser::evaluate($string);
        $this->assertEquals('3', (string) $results[0]->getResult());
    }

    function testDivisionByZero()
    {
        $string = '1 / 0';
        $results = Parser::evaluate($string);
        $this->assertTrue($results[0]->isDbz());
    }

    function testTrivialInput()
    {
        $string = '((-1))';
        $results = Parser::evaluate($string);
        $this->assertEquals(0, sizeof($results));

        $string = '3i';
        $results = Parser::evaluate($string);
        $this->assertEquals(0, sizeof($results));
    }

    function testValidBrackets()
    {
        $string = '(1+1)';
        $results = Parser::evaluate($string);
        $this->assertEquals('2', (string) $results[0]->getResult());

        $string = '1 + (2 + 3)';
        $results = Parser::evaluate($string);
        $this->assertEquals('6', (string) $results[0]->getResult());

        $string = '(2+4) / (6*(2+3))';
        $results = Parser::evaluate($string);
        $this->assertEquals('1/5', (string) $results[0]->getResult());
    }

    function testInvalidBrackets()
    {
        $string = '((1+1)';
        $results = Parser::evaluate($string);
        $this->assertEquals(0, sizeof($results));

        $string = '(1+()+1)';
        $results = Parser::evaluate($string);
        $this->assertEquals(0, sizeof($results));
    }

    function testOrderOfOperations()
    {
        $string = '4/2 + 1';
        $results = Parser::evaluate($string);
        $this->assertEquals('3', (string) $results[0]->getResult());

        $string = '1 + 4/2';
        $results = Parser::evaluate($string);
        $this->assertEquals('3', (string) $results[0]->getResult());
    }

    function testRealNumbers()
    {
        $string = '3.4 + 2.1';
        $results = Parser::evaluate($string);
        $this->assertEquals('5.5', (string) $results[0]->getResult());

        $string = '3 + 2.1';
        $results = Parser::evaluate($string);
        $this->assertEquals('5.1', (string) $results[0]->getResult());
    }

    function testRationalNumbers()
    {
        $string = '5/3 + 1/6';
        $results = Parser::evaluate($string);
        $this->assertEquals('1 5/6', (string) $results[0]->getResult());
    }

    function testComplexNumbers()
    {
        $string = '1+2i';
        $results = Parser::evaluate($string);
        $this->assertEquals('1 + 2 i', (string) $results[0]->getResult());

        $string = '(1+2i)*i';
        $results = Parser::evaluate($string);
        $this->assertEquals('- 2 + i', (string) $results[0]->getResult());
    }
}
