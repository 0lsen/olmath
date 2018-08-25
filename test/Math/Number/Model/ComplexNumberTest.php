<?php

use Math\Number\Model\ComplexNumber;
use Math\Number\Model\RationalNumber;
use Math\Number\Model\RealNumber;
use PHPUnit\Framework\TestCase;

class ComplexNumberTest extends TestCase
{

    private $testNumbers = [
        ['zero - zero', 0, 0],
        ['zero - pos', 0, 3.4],
        ['zero - neg', 0, -3.4],
        ['pos - zero', 1.2, 0],
        ['neg - zero', -1.2, 0],
        ['pos - pos', 1.2, 3.4],
        ['pos - neg', 1.2, -3.4],
        ['neg - pos', -1.2, 3.4],
        ['neg - neg', -1.2, -3.4],
    ];

    //TODO: testConstruct()

    public function testToString()
    {
        $number = new ComplexNumber(0, 0);
        $this->assertEquals("0", (string) $number);

        $number = new ComplexNumber(1, 0);
        $this->assertEquals("1", (string) $number);

        $number = new ComplexNumber(0, 1);
        $this->assertEquals("i", (string) $number);

        $number = new ComplexNumber(1, 1);
        $this->assertEquals("1 + i", (string) $number);

        $number = new ComplexNumber(1, -1);
        $this->assertEquals("1 - i", (string) $number);

        $number = new ComplexNumber(1.3, 1.3);
        $this->assertEquals("1.3 + 1.3 i", (string) $number);

        $number = new ComplexNumber(-1.3, -1.3);
        $this->assertEquals("-1.3 -1.3 i", (string) $number);

        $number = new ComplexNumber(new RationalNumber(4,3,1), new RationalNumber(4, 3, 1));
        $this->assertEquals("1 1/3 + 1 1/3 i", (string) $number);

        $number = new ComplexNumber(new RationalNumber(4,3,-1), new RationalNumber(4, 3, -1));
        $this->assertEquals("- 1 1/3 - 1 1/3 i", (string) $number);
    }

    public function testValue()
    {
        foreach ($this->testNumbers as $index => $test) {
            $number = new ComplexNumber($test[1], $test[2]);
            if (!$test[2]) {
                $this->assertEquals($this->testNumbers[$index][1], $number->value());
            } else {
                $this->assertEquals($this->testNumbers[$index][1], $number->value()->r->value());
                $this->assertEquals($this->testNumbers[$index][2], $number->value()->i->value());
            }
        }
    }

    public function testAbsoluteValue()
    {
        $sqrt13 = sqrt(13);
        $expectedValues = [
            ['zero - zero', 0],
            ['zero - zero', 3.4],
            ['zero - zero', 3.4],
            ['zero - zero', 1.2],
            ['zero - zero', 1.2],
            ['zero - zero', $sqrt13],
            ['zero - zero', $sqrt13],
            ['zero - zero', $sqrt13],
            ['zero - zero', $sqrt13],
        ];

        foreach ($this->testNumbers as $index => $test) {
            $number = new ComplexNumber($test[1],$test[2]);
            $this->assertEquals($expectedValues[$index][1], $number->absoluteValue());
        }
    }

    public function testEquals()
    {
        $number1 = new ComplexNumber(0);
        $number2 = new ComplexNumber(0, 0);
        $this->assertTrue($number1->equals($number2));
        $this->assertTrue($number2->equals($number1));

        $number1 = new ComplexNumber(1.23, -4.56);
        $number2 = new ComplexNumber(1.230, -4.560);
        $this->assertTrue($number1->equals($number2));
        $this->assertTrue($number2->equals($number1));

        $number1 = new ComplexNumber(new RationalNumber(3, 1), new RationalNumber(4,5, -1));
        $number2 = new ComplexNumber(new RationalNumber(3, 1, 1), new RationalNumber(-4, 5));
        $this->assertTrue($number1->equals($number2));
        $this->assertTrue($number2->equals($number1));

        $number1 = new ComplexNumber(new RationalNumber(1, 5), -0.8);
        $number2 = new ComplexNumber(new RealNumber(0.2), new RationalNumber(4, 5, -1));
        $this->assertTrue($number1->equals($number2));
        $this->assertTrue($number2->equals($number1));

        $number1 = new ComplexNumber(new RationalNumber(1, 5));
        $number2 = new RationalNumber(1, 5);
        $this->assertTrue($number1->equals($number2));
        $this->assertTrue($number2->equals($number1));

        $number1 = new ComplexNumber(new RationalNumber(1, 5));
        $number2 = new RealNumber(0.2);
        $this->assertTrue($number1->equals($number2));
        $this->assertTrue($number2->equals($number1));
    }

    public function testNotEquals()
    {
        $number1 = new ComplexNumber(0);
        $number2 = new ComplexNumber(0, 1);
        $this->assertFalse($number1->equals($number2));
        $this->assertFalse($number2->equals($number1));

        $number1 = new ComplexNumber(0, -1);
        $number2 = new ComplexNumber(0, 1);
        $this->assertFalse($number1->equals($number2));
        $this->assertFalse($number2->equals($number1));
    }

    public function testNegative()
    {
        $expectedValues = [
            ['zero - zero', 0, 0],
            ['zero - pos', 0, -3.4],
            ['zero - neg', 0, 3.4],
            ['pos - zero', -1.2, 0],
            ['neg - zero', 1.2, 0],
            ['pos - pos', -1.2, -3.4],
            ['pos - neg', -1.2, 3.4],
            ['neg - pos', 1.2, -3.4],
            ['neg - neg', 1.2, 3.4],
        ];

        foreach ($this->testNumbers as $index => $test) {
            $number = new ComplexNumber($test[1], $test[2]);
            $number->negative();
            $this->assertEquals($expectedValues[$index][1], $number->r->value());
            $this->assertEquals($expectedValues[$index][2], $number->i->value());
        }
    }

    public function testAdd()
    {
        foreach ($this->testNumbers as $index1 => $test1) {
            $summand1 = new ComplexNumber($test1[1], $test1[2]);
            foreach ($this->testNumbers as $index2 => $test2) {
                $summand2 = new ComplexNumber($test2[1], $test2[2]);
                $sum = $summand1->add_($summand2);
                $this->assertEquals($test1[1]+$test2[1], $sum->r->value());
                $this->assertEquals($test1[2]+$test2[2], $sum->i->value());
            }
        }
    }

    public function testMultiplyWith()
    {
        $multiplyR = function ($r1, $i1, $r2, $i2) { return $r1*$r2 - $i1*$i2; };
        $multiplyI = function ($r1, $i1, $r2, $i2) { return $r1*$i2 + $r2*$i1; };

        foreach ($this->testNumbers as $index1 => $test1) {
            $factor1 = new ComplexNumber($test1[1], $test1[2]);
            foreach ($this->testNumbers as $index2 => $test2) {
                $factor2 = new ComplexNumber($test2[1], $test2[2]);
                $product = $factor1->multiplyWith_($factor2);
                $this->assertEquals($multiplyR($test1[1], $test1[2], $test2[1], $test2[2]), $product->r->value());
                $this->assertEquals($multiplyI($test1[1], $test1[2], $test2[1], $test2[2]), $product->i->value());
            }
        }
    }

    public function testDivideBy()
    {
        $divideR = function ($r1, $i1, $r2, $i2) { return ($r1*$r2 + $i1*$i2) / ($r2*$r2 + $i2*$i2); };
        $divideI = function ($r1, $i1, $r2, $i2) { return ($r2*$i1 - $r1*$i2) / ($r2*$r2 + $i2*$i2); };

        foreach ($this->testNumbers as $index1 => $test1) {
            $dividend = new ComplexNumber($test1[1], $test1[2]);
            foreach ($this->testNumbers as $index2 => $test2) {
                $divisor = new ComplexNumber($test2[1], $test2[2]);
                if ($test2[1] == 0 && $test2[2] == 0) {
                    try {
                        $dividend->divideBy_($divisor);
                    } catch (Throwable $t) {
                        $this->assertEquals("Math\Number\Exception\DivisionByZeroException", get_class($t));
                    }
                } else {
                    $quotient = $dividend->divideBy_($divisor);
                    $this->assertEquals($divideR($test1[1], $test1[2], $test2[1], $test2[2]), $quotient->r->value());
                    $this->assertEquals($divideI($test1[1], $test1[2], $test2[1], $test2[2]), $quotient->i->value());
                }

            }
        }
    }
}
