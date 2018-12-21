<?php

use Math\Parser\FormulaParser;
use PHPUnit\Framework\TestCase;

class FormulaParserTest extends TestCase
{
    /** @var FormulaParser */
    private $parser;

    function setUp()
    {
        $this->parser = new FormulaParser(',', '?');
        parent::setUp();
    }

    function testOperators()
    {
        $string = '1 + 2';
        $result = $this->parser->evaluate($string);
        $this->assertEquals('3', (string) $result->getEntries()[0]->getResult());

        $string = '3 - 1';
        $result = $this->parser->evaluate($string);
        $this->assertEquals('2', (string) $result->getEntries()[0]->getResult());

        $string = '3 * 2';
        $result = $this->parser->evaluate($string);
        $this->assertEquals('6', (string) $result->getEntries()[0]->getResult());

        $string = '6 / 2';
        $result = $this->parser->evaluate($string);
        $this->assertEquals('3', (string) $result->getEntries()[0]->getResult());
    }

    function testDivisionByZero()
    {
        $string = '1 / 0';
        $result = $this->parser->evaluate($string);
        $this->assertTrue($result->getEntries()[0]->isDbz());
    }

    function testTrivialInput()
    {
        $string = 'foo ((-1)) bar';
        $result = $this->parser->evaluateFulltext($string);
        $this->assertEquals(0, sizeof($result->getEntries()));

        $string = '((-1))';
        $result = $this->parser->evaluate($string);
        $this->assertEquals('-1', (string) $result->getEntries()[0]->getResult());
    }

    function testValidBrackets()
    {
        $string = '(1+1)';
        $result = $this->parser->evaluate($string);
        $this->assertEquals('2', (string) $result->getEntries()[0]->getResult());

        $string = '1 + (2 + 3)';
        $result = $this->parser->evaluate($string);
        $this->assertEquals('6', (string) $result->getEntries()[0]->getResult());

        $string = '(2+4) / (6*(2+3))';
        $result = $this->parser->evaluate($string);
        $this->assertEquals('1/5', (string) $result->getEntries()[0]->getResult());

        $string = '1 + -(i + 1 - 1)';
        $result = $this->parser->evaluate($string);
        $this->assertEquals('1 - i', (string) $result->getEntries()[0]->getResult());
    }

    function testInvalidBrackets()
    {
        $this->expectParserException('((1+1)');
        $this->expectParserException('(1+()+1)');
    }

    private function expectParserException($string)
    {
        try {
            $this->parser->evaluate($string);
            $this->fail();
        } catch (Exception $exception) {
            $this->assertInstanceOf(\Math\Exception\ParserException::class, $exception);
        }
    }

    function testOrderOfOperations()
    {
        $string = '4/2 + 1';
        $result = $this->parser->evaluate($string);
        $this->assertEquals('3', (string) $result->getEntries()[0]->getResult());

        $string = '1 + 4/2';
        $result = $this->parser->evaluate($string);
        $this->assertEquals('3', (string) $result->getEntries()[0]->getResult());
    }

    function testRealNumbers()
    {
        $string = '3.4 + 2.1';
        $result = $this->parser->evaluate($string);
        $this->assertEquals('5.5', (string) $result->getEntries()[0]->getResult());

        $string = '3 + 2.1';
        $result = $this->parser->evaluate($string);
        $this->assertEquals('5.1', (string) $result->getEntries()[0]->getResult());
    }

    function testRationalNumbers()
    {
        $string = '5/3 + 1/6';
        $result = $this->parser->evaluate($string);
        $this->assertEquals('1 5/6', (string) $result->getEntries()[0]->getResult());
    }

    function testComplexNumbers()
    {
        $string = '1+2i';
        $result = $this->parser->evaluate($string);
        $this->assertEquals('1 + 2 i', (string) $result->getEntries()[0]->getResult());

        $string = '(1+2i)*i';
        $result = $this->parser->evaluate($string);
        $this->assertEquals('-2 + i', (string) $result->getEntries()[0]->getResult());
    }

    function testReplacements()
    {
        $string = '1,1 + 1';
        $result = $this->parser->evaluate($string);
        $this->assertEquals('2.1', (string) $result->getEntries()[0]->getResult());

        $string = '1/3 i';
        $result = $this->parser->evaluate($string);
        $this->assertEquals('1/3 i', (string) $result->getEntries()[0]->getResult());

        $string = '2(1+1)';
        $result = $this->parser->evaluate($string);
        $this->assertEquals('4', (string) $result->getEntries()[0]->getResult());

        $string = '(1+2)i';
        $result = $this->parser->evaluate($string);
        $this->assertEquals('3 i', (string) $result->getEntries()[0]->getResult());
    }

    function testUnaryOperators()
    {
        $string = '-(1+2)';
        $result = $this->parser->evaluate($string);
        $this->assertEquals('-3', (string) $result->getEntries()[0]->getResult());

        $string = '-(-(1+2)--(3+4))';
        $result = $this->parser->evaluate($string);
        $this->assertEquals('-4', (string) $result->getEntries()[0]->getResult());
    }

    function testVariables()
    {
        $string = "a123 = 1 + 2 \n a123 = a123 + 1 \n a12 = a123 + 3 \n ba123 = 4 \n 1 / 0 \n a123 + a12 + ba123 + 1";
        $result = $this->parser->evaluate($string);
        $this->assertEquals('a123 = 4', (string) $result->getEntries()['a123']);
        $this->assertEquals('a12 = 7', (string) $result->getEntries()['a12']);
        $this->assertEquals('ba123 = 4', (string) $result->getEntries()['ba123']);
        $this->assertEquals('', (string) $result->getEntries()[0]);
        $this->assertEquals('a123 + a12 + ba123 + 1 = 16', (string) $result->getEntries()[1]);

        $this->expectParserException("i = 1");
        $this->expectParserException("1 + a");
        $this->expectParserException("foo \n 1 + 1");
    }

    function testFulltext()
    {
        $string = "foo 1 +1 bar \n foo \n a123 = 1 + 2 \n a123 + 3";
        $result = $this->parser->evaluateFulltext($string);
        $this->assertEquals('a123 = 3', (string) $result->getEntries()['a123']);
        $this->assertEquals('a123 + 3 = 6', (string) $result->getEntries()[0]);

        $string = "foo 1 +1 bar 1+ 2";
        $result = $this->parser->evaluateFulltext($string);
        $this->assertEquals('1 +1 = 2', (string) $result->getEntries()[0]);
        $this->assertEquals('1+ 2 = 3', (string) $result->getEntries()[1]);
    }
}
