<?php

use Math\Functions\Number\Root;
use PHPUnit\Framework\TestCase;

class RootTest extends TestCase
{
    public function testNthRoot()
    {
        $this->assertEquals(3, Root::nthRoot(9, 2));
        $this->assertEquals(3, Root::nthRoot(27, 3));
        $this->assertEquals(0.1, Root::nthRoot(0.01, 2));
        $this->assertEquals(0.1, Root::nthRoot(0.001, 3));
        $this->assertEquals(0, Root::nthRoot(0, 666));
        $this->assertEquals(6, Root::nthRoot(6,  1));
        $this->assertEquals(1, Root::nthRoot(1,  666));
    }
}
