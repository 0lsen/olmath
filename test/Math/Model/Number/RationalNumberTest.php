<?php

use Math\Model\Number\ComplexNumber;
use Math\Model\Number\RationalNumber;
use Math\Model\Number\RealNumber;
use PHPUnit\Framework\TestCase;

class RationalNumberTest extends TestCase
{
    private $testNumbers = [
        [0, 0, 0],
        [1, 3, 1],
        [1, 5, 1],
        [-1, 3, 1],
        [-1, 5, 1],
        [1, 3, 5],
        [1, 4, 7],
        [-1, 3, 7],
        [-1, 4, 7],
    ];

    private function numberValue($array) {
        if (!$array[0]) {
            return 0;
        } else {
            return ($array[0] > 0 ? 1 : -1) * $array[1] / $array[2];
        }
    }

    //TODO: testConstruct()

    public function testToString()
    {
        $number = new RationalNumber(0, 0, 0);
        $this->assertEquals("0", (string) $number);

        $number = new RationalNumber(1, 3, 1);
        $this->assertEquals("1/3", (string) $number);

        $number = new RationalNumber(1, 3, -1);
        $this->assertEquals("- 1/3", (string) $number);

        $number = new RationalNumber(6, 3, 1);
        $this->assertEquals("2", (string) $number);

        $number = new RationalNumber(6, 3, -1);
        $this->assertEquals("- 2", (string) $number);

        $number = new RationalNumber(7, 3, 1);
        $this->assertEquals("2 1/3", (string) $number);

        $number = new RationalNumber(7, 3, -1);
        $this->assertEquals("- 2 1/3", (string) $number);
    }

    public function testValue()
    {
        foreach ($this->testNumbers as $index => $test) {
            $number = new RationalNumber($test[1], $test[2], $test[0]);
            $this->assertEquals($this->numberValue($test), $number->value());
        }
    }

    public function testAbsoluteValue()
    {
        foreach ($this->testNumbers as $index => $test) {
            $number = new RationalNumber($test[1], $test[2], $test[0]);
            $this->assertEquals(abs($this->numberValue($test)), $number->absoluteValue());
        }
    }

    public function testZeroEquals()
    {
        $number1 = new RationalNumber(0);
        $number2 = new RationalNumber(1, 2, 0);
        $this->assertTrue($number1->equals($number2));
        $this->assertTrue($number2->equals($number1));

        $number1 = new RationalNumber(0);
        $number2 = new RealNumber(0);
        $this->assertTrue($number1->equals($number2));
        $this->assertTrue($number2->equals($number1));

        $number1 = new RationalNumber(0);
        $number2 = new ComplexNumber(0, new RationalNumber(0));
        $this->assertTrue($number1->equals($number2));
        $this->assertTrue($number2->equals($number1));
    }

    public function testEquals()
    {
        $number1 = new RationalNumber(1, 5, -1);
        $number2 = new RationalNumber(1, 5, -1);
        $this->assertTrue($number1->equals($number2));
        $this->assertTrue($number2->equals($number1));

        $number1 = new RationalNumber(1, 5, -1);
        $number2 = new RealNumber(-0.2);
        $this->assertTrue($number1->equals($number2));
        $this->assertTrue($number2->equals($number1));

        $number1 = new RationalNumber(1, 5, -1);
        $number2 = new ComplexNumber(-0.2, 0);
        $this->assertTrue($number1->equals($number2));
        $this->assertTrue($number2->equals($number1));
    }

    public function testNotEquals()
    {
        $number1 = new RationalNumber(1, 3, 1);
        $number2 = new RationalNumber(2, 5, 1);
        $this->assertFalse($number1->equals($number2));
        $this->assertFalse($number2->equals($number1));

        $number1 = new RationalNumber(1, 3, 1);
        $number2 = new RealNumber(0.33333);
        $this->assertFalse($number1->equals($number2));
        $this->assertFalse($number2->equals($number1));

        $number1 = new RationalNumber(1, 5, -1);
        $number2 = new ComplexNumber(-0.2, 1);
        $this->assertFalse($number1->equals($number2));
        $this->assertFalse($number2->equals($number1));
    }

    public function testCompare()
    {
        foreach ($this->testNumbers as $index1 => $test1) {
            $number1 = new RationalNumber($test1[1], $test1[2], $test1[0]);
            foreach ($this->testNumbers as $index2 => $test2) {
                $number2 = new RationalNumber($test2[1], $test2[2], $test2[0]);
                $this->assertEquals(
                    $this->numberValue($test1) <=> $this->numberValue($test2),
                    $number1->compareTo($number2)
                );
            }
        }
    }

    public function testNegative()
    {
        foreach ($this->testNumbers as $index => $test) {
            $number = new RationalNumber($test[1], $test[2], $test[0]);
            $this->assertEquals(-1*$this->numberValue($test), $number->negative()->value());
        }
    }

    public function testAdd()
    {
        foreach ($this->testNumbers as $index1 => $test1) {
            $summand1 = new RationalNumber($test1[1], $test1[2], $test1[0]);
            foreach ($this->testNumbers as $index2 => $test2) {
                $summand2 = new RationalNumber($test2[1], $test2[2], $test2[0]);
                $this->assertEquals(
                    $this->numberValue($test1) + $this->numberValue($test2),
                    $summand1->add_($summand2)->value()
                );
            }
        }
    }

    public function testMultiplyWith()
    {
        foreach ($this->testNumbers as $index1 => $test1) {
            $factor1 = new RationalNumber($test1[1], $test1[2], $test1[0]);
            foreach ($this->testNumbers as $index2 => $test2) {
                $factor2 = new RationalNumber($test2[1], $test2[2], $test2[0]);
                $this->assertEquals(
                    $this->numberValue($test1) * $this->numberValue($test2),
                    $factor1->multiplyWith_($factor2)->value()
                );
            }
        }
    }

    public function testReciprocal()
    {
        $this->assertEquals(2, (new RationalNumber(1,2))->reciprocal()->value());
        $this->assertEquals(-2, (new RationalNumber(1,2, -1))->reciprocal()->value());
        $this->assertEquals(0.5, (new RationalNumber(2,1))->reciprocal()->value());
        $this->assertEquals(-0.5, (new RationalNumber(2,1, -1))->reciprocal()->value());

        try {
            (new RationalNumber(0))->reciprocal();
        } catch (\Throwable $t) {
            $this->assertEquals("Math\Exception\DivisionByZeroException", get_class($t));
        }

        try {
            (new RationalNumber(1, 0))->reciprocal();
        } catch (\Throwable $t) {
            $this->assertEquals("Math\Exception\DivisionByZeroException", get_class($t));
        }
    }
}
