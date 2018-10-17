<?php

use Math\Model\Number\ComplexNumber;
use Math\Model\Number\RationalNumber;
use Math\Model\Number\RealNumber;
use Math\Model\Vector\Vector;
use PHPUnit\Framework\TestCase;

class VectorTest extends TestCase
{
    public function testNorm()
    {
        $vector = new Vector(
            new RationalNumber(2,3, 1),
            new RationalNumber(-7,4, -1)
        );
        $this->assertEquals(1.872683754520353, $vector->norm()->value());

        $vector = new Vector(
            new RealNumber(1.5),
            new RealNumber(-2.7)
        );
        $this->assertEquals(3.0886890422961, $vector->norm()->value());

        $vector = new Vector(
            new RationalNumber(0),
            new RationalNumber(0)
        );
        $this->assertEquals(0, $vector->norm()->value());

        $vector = new Vector(
            new RationalNumber(1),
            new RealNumber(0),
            new RealNumber(2),
            new RationalNumber(-2)
        );
        $this->assertEquals(3, $vector->norm()->value());

        $vector = new Vector(
            new ComplexNumber(new RationalNumber(2,3, 1), new RationalNumber(1, 3, 1)),
            new ComplexNumber(new RationalNumber(3,4, -1), new RationalNumber(1,7, -1))
        );
        $this->assertEquals(1.066988153083651, $vector->norm()->value());

        $vector = new Vector(
            new ComplexNumber(new RealNumber(1), new RationalNumber(1,2, 1)),
            new RealNumber(-2.7)
        );
        $this->assertEquals(2.922327839240492, $vector->norm()->value());

        $vector = new Vector();
        $this->assertEquals(0, $vector->norm()->value());
    }

    //TODO: testScalarMultiplyWith()

    //TODO: testAddVector()
}