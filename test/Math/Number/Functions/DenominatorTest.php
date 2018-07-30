<?php

use Math\Number\Functions\Denominator;
use PHPUnit\Framework\TestCase;

class DenominatorTest extends TestCase
{
    public function testLCM()
    {
        $this->assertEquals(15, Denominator::LCM(3, 5));
        $this->assertEquals(15, Denominator::LCM(5, 3));
        $this->assertEquals(12, Denominator::LCM(6, 4));
        $this->assertEquals(12, Denominator::LCM(4, 6));
    }

    public function testGCD()
    {
        $this->assertEquals(1, Denominator::GCD(3, 5));
        $this->assertEquals(1, Denominator::GCD(5, 3));
        $this->assertEquals(2, Denominator::GCD(6, 4));
        $this->assertEquals(2, Denominator::GCD(4, 6));
    }
}
