<?php

use Math\Model\Number\RealNumber;
use PHPUnit\Framework\TestCase;

class RealNumberTest extends TestCase
{
    /* TODO:
     * - rounding of compared values
     * - test scenarios for incompatible operands
     * - construct with different types
     */

    private $testNumbers = [
        ['positive int', 3],
        ['positive double', 3.14],
        ['negative int', -3],
        ['negative double', -3.14],
        ['zero', 0],
    ];

    public function testUnderScoreCall() {
        $n1 = new RealNumber(1);
        $n2 = new RealNumber(2);
        $n3 = $n1->add_($n2);
        $this->assertEquals(3, $n3->value());
        $this->assertEquals(1, $n1->value());

        $n4 = $n1->negative_();
        $this->assertEquals(-1, $n4->value());
        $this->assertEquals(1, $n1->value());
    }

    public function testValue()
    {
        $expectedValues = [
            3,
            3.14,
            -3,
            -3.14,
            0,
        ];

        foreach ($this->testNumbers as $index => $test) {
            $number = new RealNumber($test[1]);
            $this->assertEquals($expectedValues[$index], $number->value());
        }
    }

    public function testAbsoluteValue()
    {
        $expectedValues = [
            3,
            3.14,
            3,
            3.14,
            0,
        ];

        foreach ($this->testNumbers as $index => $test) {
            $number = new RealNumber($test[1]);
            $this->assertEquals($expectedValues[$index], $number->absoluteValue());
        }
    }

    public function testEquals()
    {
        $n1 = new RealNumber(1);
        $n2 = new RealNumber(1.0);
        $n3 = new RealNumber(2);

        $this->assertTrue($n1->equals($n2));
        $this->assertTrue($n2->equals($n1));
        $this->assertFalse($n1->equals($n3));
        $this->assertFalse($n3->equals($n1));
    }

    public function testCompare()
    {
        $expectedValues = [
            [0, -1, 1, 1, 1],
            [1, 0, 1, 1, 1],
            [-1, -1, 0, 1, -1],
            [-1, -1, -1, 0, -1],
            [-1, -1, 1, 1, 0],
        ];

        foreach ($this->testNumbers as $index1 => $test1) {
            $number1 = new RealNumber($test1[1]);
            foreach ($this->testNumbers as $index2 => $test2) {
                $number2 = new RealNumber($test2[1]);
                $this->assertEquals($expectedValues[$index1][$index2], $number1->compareTo($number2));
            }
        }
    }

    public function testNegative()
    {
        $expectedValues = [
            -3,
            -3.14,
            3,
            3.14,
            0,
        ];

        foreach ($this->testNumbers as $index => $test) {
            $number = new RealNumber($test[1]);
            $this->assertEquals($expectedValues[$index], $number->negative()->value());
        }
    }

    public function testAdd()
    {
        $expectedValues = [
            [
                6,
                6.14,
                0,
                -0.14,
                3,
            ],
            [
                6.14,
                6.28,
                0.14,
                0,
                3.14,
            ],
            [
                0,
                0.14,
                -6,
                -6.14,
                -3,
            ],
            [
                -0.14,
                0,
                -6.14,
                -6.28,
                -3.14,
            ],
            [
                3,
                3.14,
                -3,
                -3.14,
                0,
            ],
        ];

        foreach ($this->testNumbers as $index1 => $test1) {
            $summand1 = new RealNumber($test1[1]);
            foreach ($this->testNumbers as $index2 => $test2) {
                $summand2 = new RealNumber($test2[1]);
                $this->assertEquals($expectedValues[$index1][$index2], $summand1->add_($summand2)->value());
            }
        }
    }

    public function testMultiplyWith()
    {
        $expectedValues = [
            [
                9,
                9.42,
                -9,
                -9.42,
                0,
            ],
            [
                9.42,
                9.8596,
                -9.42,
                -9.8596,
                0,
            ],
            [
                -9,
                -9.42,
                9,
                9.42,
                0,
            ],
            [
                -9.42,
                -9.8596,
                9.42,
                9.8596,
                0,
            ],
            [
                0,
                0,
                0,
                0,
                0,
            ],
        ];

        foreach ($this->testNumbers as $index1 => $test1) {
            $factor1 = new RealNumber($test1[1]);
            foreach ($this->testNumbers as $index2 => $test2) {
                $factor2 = new RealNumber($test2[1]);
                $this->assertEquals($expectedValues[$index1][$index2], $factor1->multiplyWith_($factor2)->value());
            }
        }
    }

    public function testDivideBy()
    {
        $expectedValues = [
            [
                1,
                0.95541401273885,
                -1,
                -0.95541401273885,
                false,
            ],
            [
                1.0466666666667,
                1,
                -1.0466666666667,
                -1,
                false,
            ],
            [
                -1,
                -0.95541401273885,
                1,
                0.95541401273885,
                false,
            ],
            [
                -1.0466666666667,
                -1,
                1.0466666666667,
                1,
                false,
            ],
            [
                0,
                0,
                0,
                0,
                false,
            ],
        ];

        foreach ($this->testNumbers as $index1 => $test1) {
            $dividend = new RealNumber($test1[1]);
            foreach ($this->testNumbers as $index2 => $test2) {
                $divisor = new RealNumber($test2[1]);
                if ($expectedValues[$index1][$index2] === false) {
                    try {
                        $dividend->divideBy_($divisor);
                    } catch (Throwable $t) {
                        $this->assertEquals("Math\Exception\DivisionByZeroException", get_class($t));
                    }
                } else {
                    $this->assertEquals($expectedValues[$index1][$index2], $dividend->divideBy_($divisor)->value());
                }
            }
        }
    }
}
