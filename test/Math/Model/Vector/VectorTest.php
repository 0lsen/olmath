<?php

use Math\Model\Number\ComplexNumber;
use Math\Model\Number\RationalNumber;
use Math\Model\Number\RealNumber;
use Math\Model\Number\Zero;
use Math\Model\Vector\Vector;
use PHPUnit\Framework\TestCase;

class VectorTest extends TestCase
{
    public function testToString()
    {
        $vector = new Vector(
            new RealNumber(1.2),
            Zero::getInstance(),
            new RealNumber(-3.4)
        );

        $this->assertEquals("[ 1.2 ; 0 ; -3.4 ]", (string) $vector);
    }

    public function testInvoke()
    {
        $one = new RationalNumber(1);
        $two = new RationalNumber(2);
        $vector = new Vector($one, $two);

        $this->assertSame($one, $vector(1));
        $this->assertSame($two, $vector(2));
    }

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
            Zero::getInstance(),
            Zero::getInstance()
        );
        $this->assertEquals(0, $vector->norm()->value());

        $vector = new Vector(
            new RationalNumber(1),
            Zero::getInstance(),
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

    public function testScalarMultiplyWith()
    {
        $numberReal = new RealNumber(1.3);
        $numberRational = new RationalNumber(1, 4, -1);
        $numberComplex = new ComplexNumber($numberReal, $numberRational);
        $vector = new Vector(
            clone $numberReal,
            clone $numberRational,
            clone $numberComplex,
            Zero::getInstance()
        );

        $multReal = $vector->multiplyWithScalar_($numberReal);
        $multRational = $vector->multiplyWithScalar_($numberRational);
        $multComplex = $vector->multiplyWithScalar_($numberComplex);
        $multZero = $vector->multiplyWithScalar_(Zero::getInstance());

        $this->assertEquals(1.69, $multReal->get(1)->value());
        $this->assertEquals(-0.325, $multReal->get(2)->value());
        $this->assertEquals(1.69, $multReal->get(3)->getR()->value());
        $this->assertEquals(-0.325, $multReal->get(3)->getI()->value());
        $this->assertEquals(0, $multReal->get(4)->value());

        $this->assertEquals(-0.325, $multRational->get(1)->value());
        $this->assertEquals(0.0625, $multRational->get(2)->value());
        $this->assertEquals(-0.325, $multRational->get(3)->getR()->value());
        $this->assertEquals(0.0625, $multRational->get(3)->getI()->value());
        $this->assertEquals(0, $multRational->get(4)->value());

        $this->assertEquals(1.69, $multComplex->get(1)->getR()->value());
        $this->assertEquals(-0.325, $multComplex->get(1)->getI()->value());
        $this->assertEquals(-0.325, $multComplex->get(2)->getR()->value());
        $this->assertEquals(0.0625, $multComplex->get(2)->getI()->value());
        $this->assertEquals(1.69-0.0625, $multComplex->get(3)->getR()->value());
        $this->assertEquals(-0.65, $multComplex->get(3)->getI()->value());
        $this->assertEquals(0, $multComplex->get(4)->value());

        $this->assertEquals(0, $multZero->get(1)->value());
        $this->assertEquals(0, $multZero->get(2)->value());
        $this->assertEquals(0, $multZero->get(3)->value());
        $this->assertEquals(0, $multZero->get(4)->value());
    }

    public function testAddVector()
    {
        $numberReal = new RealNumber(1.3);
        $numberRational = new RationalNumber(1, 4, -1);
        $numberComplex = new ComplexNumber($numberReal, $numberRational);

        $vector1 = new Vector(
            clone $numberReal,
            clone $numberRational,
            clone $numberComplex,
            clone Zero::getInstance()
        );

        $vector2 = new Vector(
            clone $numberRational,
            clone $numberComplex,
            Zero::getInstance(),
            clone $numberReal
        );

        $vector1->addVector($vector2);

        $this->assertEquals(1.05, $vector1->get(1)->value());
        $this->assertEquals(1.05, $vector1->get(2)->getR()->value());
        $this->assertEquals(-0.25, $vector1->get(2)->getI()->value());
        $this->assertEquals(1.3, $vector1->get(3)->getR()->value());
        $this->assertEquals(-0.25, $vector1->get(3)->getI()->value());
        $this->assertEquals(1.3, $vector1->get(4)->value());
    }

    //TODO: testGetDim()
    //TODO: testGet()
    //TODO: testSet()
    //TODO: testAppendNumber()
    //TODO: testAppendVector()
    //TODO: testNormalise()
    //TODO: testDimensionExceptions()
}