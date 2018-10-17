<?php

use Math\Functions\CalcUtil;
use PHPUnit\Framework\TestCase;

class CalcUtilTest extends TestCase
{
    public function testNthRoot()
    {
        $this->assertEquals(3, CalcUtil::nthRoot(9, 2));
        $this->assertEquals(3, CalcUtil::nthRoot(27, 3));
        $this->assertEquals(0.1, CalcUtil::nthRoot(0.01, 2));
        $this->assertEquals(0.1, CalcUtil::nthRoot(0.001, 3));
        $this->assertEquals(0, CalcUtil::nthRoot(0, 666));
        $this->assertEquals(6, CalcUtil::nthRoot(6,  1));
        $this->assertEquals(1, CalcUtil::nthRoot(1,  666));
    }
}
