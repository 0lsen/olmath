<?php

use Math\Functions\Numeric\LinearSystemSolvers;
use Math\Model\Matrix\SparseInput\SingleElement;
use Math\Model\Matrix\SparseMatrix;
use Math\Model\Number\RationalNumber;
use Math\Model\Vector\Vector;
use PHPUnit\Framework\TestCase;

class LinearSystemSolversTest extends TestCase
{
    public function testLsmr()
    {
        $A = new SparseMatrix(4,4,
            new SingleElement(1,1,  new RationalNumber(1)),
            new SingleElement(2,2,  new RationalNumber(1)),
            new SingleElement(3,3,  new RationalNumber(1)),
            new SingleElement(4,4,  new RationalNumber(1))
        );

        $b = new Vector(
            new RationalNumber(1),
            new RationalNumber(2),
            new RationalNumber(3),
            new RationalNumber(4)
        );

        $x = LinearSystemSolvers::LSMR($A, $b);

        $this->assertEquals(4, $x->getDim());
        $this->assertEquals(1, $x->get(1)->value());
        $this->assertEquals(2, $x->get(2)->value());
        $this->assertEquals(3, $x->get(3)->value());
        $this->assertEquals(4, $x->get(4)->value());
    }

    public function testLsqr()
    {
        $A = new SparseMatrix(4,4,
            new SingleElement(1,1,  new RationalNumber(1)),
            new SingleElement(2,2,  new RationalNumber(1)),
            new SingleElement(3,3,  new RationalNumber(1)),
            new SingleElement(4,4,  new RationalNumber(1))
        );

        $b = new Vector(
            new RationalNumber(1),
            new RationalNumber(2),
            new RationalNumber(3),
            new RationalNumber(4)
        );

        $x = LinearSystemSolvers::LSQR($A, $b);

        $this->assertEquals(4, $x->getDim());
        $this->assertEquals(1, $x->get(1)->value());
        $this->assertEquals(2, $x->get(2)->value());
        $this->assertEquals(3, $x->get(3)->value());
        $this->assertEquals(4, $x->get(4)->value());
    }
}
