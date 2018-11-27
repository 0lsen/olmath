<?php

use Math\Parser\NumberParser;
use PHPUnit\Framework\TestCase;

class NumberParserTest extends TestCase
{
    /** @var NumberParser */
    private $parser;

    function setUp()
    {
        $this->parser = new NumberParser(',', '?');
        parent::setUp();
    }

    function testOperators()
    {
        $string = '1 + 2';
        $results = $this->parser->evaluateFulltext($string);
        $this->assertEquals('3', (string) $results[0]->getResult());

        $string = '3 - 1';
        $results = $this->parser->evaluateFulltext($string);
        $this->assertEquals('2', (string) $results[0]->getResult());

        $string = '3 * 2';
        $results = $this->parser->evaluateFulltext($string);
        $this->assertEquals('6', (string) $results[0]->getResult());

        $string = '6 / 2';
        $results = $this->parser->evaluateFulltext($string);
        $this->assertEquals('3', (string) $results[0]->getResult());
    }

    function testDivisionByZero()
    {
        $string = '1 / 0';
        $results = $this->parser->evaluateFulltext($string);
        $this->assertTrue($results[0]->isDbz());
    }

    function testTrivialInput()
    {
        $string = '((-1))';
        $results = $this->parser->evaluateFulltext($string);
        $this->assertEquals(0, sizeof($results));

        $result = $this->parser->evaluate($string);
        $this->assertEquals('-1', (string) $result->getResult());
    }

    function testValidBrackets()
    {
        $string = '(1+1)';
        $results = $this->parser->evaluateFulltext($string);
        $this->assertEquals('2', (string) $results[0]->getResult());

        $string = '1 + (2 + 3)';
        $results = $this->parser->evaluateFulltext($string);
        $this->assertEquals('6', (string) $results[0]->getResult());

        $string = '(2+4) / (6*(2+3))';
        $results = $this->parser->evaluateFulltext($string);
        $this->assertEquals('1/5', (string) $results[0]->getResult());

        $string = '1 + -(i + 1 - 1)';
        $results = $this->parser->evaluateFulltext($string);
        $this->assertEquals('1 - i', (string) $results[0]->getResult());
    }

    function testInvalidBrackets()
    {
        $string = '((1+1)';
        $results = $this->parser->evaluateFulltext($string);
        $this->assertEquals(0, sizeof($results));

        $string = '(1+()+1)';
        $results = $this->parser->evaluateFulltext($string);
        $this->assertEquals(0, sizeof($results));
    }

    function testOrderOfOperations()
    {
        $string = '4/2 + 1';
        $results = $this->parser->evaluateFulltext($string);
        $this->assertEquals('3', (string) $results[0]->getResult());

        $string = '1 + 4/2';
        $results = $this->parser->evaluateFulltext($string);
        $this->assertEquals('3', (string) $results[0]->getResult());
    }

    function testRealNumbers()
    {
        $string = '3.4 + 2.1';
        $results = $this->parser->evaluateFulltext($string);
        $this->assertEquals('5.5', (string) $results[0]->getResult());

        $string = '3 + 2.1';
        $results = $this->parser->evaluateFulltext($string);
        $this->assertEquals('5.1', (string) $results[0]->getResult());
    }

    function testRationalNumbers()
    {
        $string = '5/3 + 1/6';
        $results = $this->parser->evaluateFulltext($string);
        $this->assertEquals('1 5/6', (string) $results[0]->getResult());
    }

    function testComplexNumbers()
    {
        $string = '1+2i';
        $results = $this->parser->evaluateFulltext($string);
        $this->assertEquals('1 + 2 i', (string) $results[0]->getResult());

        $string = '(1+2i)*i';
        $results = $this->parser->evaluateFulltext($string);
        $this->assertEquals('-2 + i', (string) $results[0]->getResult());
    }

    function testReplacements()
    {
        $string = '1,1 + 1';
        $results = $this->parser->evaluateFulltext($string);
        $this->assertEquals('2.1', (string) $results[0]->getResult());

        $string = '1/3 i';
        $results = $this->parser->evaluateFulltext($string);
        $this->assertEquals('1/3 i', (string) $results[0]->getResult());
    }

    function testUnaryOperators()
    {
        $string = '-(1+2)';
        $results = $this->parser->evaluateFulltext($string);
        $this->assertEquals('-3', (string) $results[0]->getResult());

        $string = '-(-(1+2)--(3+4))';
        $results = $this->parser->evaluateFulltext($string);
        $this->assertEquals('-4', (string) $results[0]->getResult());
    }

    function testOriginalString()
    {
        $string = 'foo 1 +1 bar';
        $results = $this->parser->evaluateFulltext($string);
        $this->assertEquals('1 +1', $results[0]->getOriginal());
    }
}
