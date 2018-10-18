<?php

use Math\Model\Number\ComplexNumber;
use Math\Model\Number\RationalNumber;
use Math\Model\Number\RealNumber;
use Math\Model\Number\Zero;
use Math\Model\Vector\SparseVector;
use PHPUnit\Framework\TestCase;

class SparseVectorTest extends TestCase
{
    public function testToString()
    {
        $vector = new SparseVector(5, [
            1 => new RealNumber(1.2),
            2 => Zero::getInstance(),
            4 => new RealNumber(-3.4)
        ]);

        $this->assertEquals("[ 1: 1.2 ; 4: -3.4 ]", (string) $vector);
    }
    public function testNorm()
    {
        $vector = new SparseVector(5, [
            1 => new RationalNumber(2,3, 1),
            4 => new RationalNumber(-7,4, -1)
        ]);
        $this->assertEquals(1.872683754520353, $vector->norm()->value());

        $vector = new SparseVector(5, [
            2 => new RealNumber(1.5),
            3=> new RealNumber(-2.7)
        ]);
        $this->assertEquals(3.0886890422961, $vector->norm()->value());

        $vector = new SparseVector(5, [
            0 => Zero::getInstance(),
            1 => Zero::getInstance()
        ]);
        $this->assertEquals(0, $vector->norm()->value());

        $vector = new SparseVector(5, [
            1 => new RationalNumber(1),
            4 => Zero::getInstance(),
            3 => new RealNumber(2),
            2 => new RationalNumber(-2)
        ]);
        $this->assertEquals(3, $vector->norm()->value());

        $vector = new SparseVector(5, [
            0 => new ComplexNumber(new RationalNumber(2,3, 1), new RationalNumber(1, 3, 1)),
            4 => new ComplexNumber(new RationalNumber(3,4, -1), new RationalNumber(1,7, -1))
        ]);
        $this->assertEquals(1.066988153083651, $vector->norm()->value());

        $vector = new SparseVector(5, [
            1 => new ComplexNumber(new RealNumber(1), new RationalNumber(1,2, 1)),
            2=> new RealNumber(-2.7)
        ]);
        $this->assertEquals(2.922327839240492, $vector->norm()->value());
    }

    public function testScalarMultiplyWith()
    {
        $numberReal = new RealNumber(1.3);
        $numberRational = new RationalNumber(1, 4, -1);
        $numberComplex = new ComplexNumber($numberReal, $numberRational);
        $vector = new SparseVector(4, [
            0 => clone $numberReal,
            1 => clone $numberRational,
            2 => clone $numberComplex
        ]);

        $multReal = $vector->multiplyWithScalar_($numberReal);
        $multRational = $vector->multiplyWithScalar_($numberRational);
        $multComplex = $vector->multiplyWithScalar_($numberComplex);
        $multZero = $vector->multiplyWithScalar_(Zero::getInstance());

        $this->assertEquals(1.69, $multReal->get(0)->value());
        $this->assertEquals(-0.325, $multReal->get(1)->value());
        $this->assertEquals(1.69, $multReal->get(2)->getR()->value());
        $this->assertEquals(-0.325, $multReal->get(2)->getI()->value());
        $this->assertEquals(0, $multReal->get(3)->value());

        $this->assertEquals(-0.325, $multRational->get(0)->value());
        $this->assertEquals(0.0625, $multRational->get(1)->value());
        $this->assertEquals(-0.325, $multRational->get(2)->getR()->value());
        $this->assertEquals(0.0625, $multRational->get(2)->getI()->value());
        $this->assertEquals(0, $multRational->get(3)->value());

        $this->assertEquals(1.69, $multComplex->get(0)->getR()->value());
        $this->assertEquals(-0.325, $multComplex->get(0)->getI()->value());
        $this->assertEquals(-0.325, $multComplex->get(1)->getR()->value());
        $this->assertEquals(0.0625, $multComplex->get(1)->getI()->value());
        $this->assertEquals(1.69-0.0625, $multComplex->get(2)->getR()->value());
        $this->assertEquals(-0.65, $multComplex->get(2)->getI()->value());
        $this->assertEquals(0, $multComplex->get(3)->value());

        $this->assertEquals(0, $multZero->get(0)->value());
        $this->assertEquals(0, $multZero->get(1)->value());
        $this->assertEquals(0, $multZero->get(2)->value());
        $this->assertEquals(0, $multZero->get(3)->value());
    }

    public function testAddVector()
    {
        $numberReal = new RealNumber(1.3);
        $numberRational = new RationalNumber(1, 4, -1);
        $numberComplex = new ComplexNumber($numberReal, $numberRational);

        $vector1 = new SparseVector(4, [
            0 => clone $numberReal,
            1 => clone $numberRational,
            2 => clone $numberComplex
        ]);

        $vector2 = new SparseVector(4, [
            0 => clone $numberRational,
            1 => clone $numberComplex,
            3 => clone $numberReal
        ]);

        $vector1->addVector($vector2);

        $this->assertEquals(1.05, $vector1->get(0)->value());
        $this->assertEquals(1.05, $vector1->get(1)->getR()->value());
        $this->assertEquals(-0.25, $vector1->get(1)->getI()->value());
        $this->assertEquals(1.3, $vector1->get(2)->getR()->value());
        $this->assertEquals(-0.25, $vector1->get(2)->getI()->value());
        $this->assertEquals(1.3, $vector1->get(3)->value());
    }

    //TODO: testGetDim()
    //TODO: testGet()
    //TODO: testSet()
    //TODO: testAppendNumber()
    //TODO: testAppendVector()
    //TODO: testDimensionExceptions()
}