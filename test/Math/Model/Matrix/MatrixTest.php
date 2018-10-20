<?php

use Math\Model\Matrix\Matrix;
use Math\Model\Matrix\MatrixInterface;
use Math\Model\Number\ComplexNumber;
use Math\Model\Number\RationalNumber;
use Math\Model\Number\RealNumber;
use Math\Model\Number\Zero;
use Math\Model\Vector\Vector;
use PHPUnit\Framework\TestCase;

class MatrixTest extends TestCase
{
    /** @var MatrixInterface */
    private $matrix;

    public function setUp()
    {
        $this->matrix = new Matrix(
            [new RealNumber(1), new RationalNumber(1, 2, 1), new ComplexNumber(new RationalNumber(2), new RationalNumber(1,4, -1))],
            [new RealNumber(4), new RealNumber(-5), new RealNumber(666)]
        );
        parent::setUp();
    }

    public function testToString()
    {
        $this->assertEquals(""
                            ."[ 1 | 1/2 | 2 - 1/4 i ]\n"
                            ."[ 4 |  -5 |       666 ]", (string) $this->matrix);
    }

    public function testTranspose()
    {
        $this->assertEquals(""
                            ."[         1 |   4 ]\n"
                            ."[       1/2 |  -5 ]\n"
                            ."[ 2 - 1/4 i | 666 ]", (string) $this->matrix->transpose_());
    }

    public function testMultiplyWithScalar()
    {
        $this->assertEquals(""
                            ."[ 2 |   1 | 4 - 1/2 i ]\n"
                            ."[ 8 | -10 |      1332 ]", (string) $this->matrix->multiplyWithScalar_(new RationalNumber(2)));
    }

    public function testMultiplyWithVector()
    {
        $vector = new Vector(
            new RationalNumber(1),
            new RationalNumber(2),
            Zero::getInstance()
        );

        $product = $this->matrix->multiplyWithVector_($vector);

        $this->assertEquals(2, $product->getDim());
        $this->assertEquals(2, $product->get(1)->value());
        $this->assertEquals(-6, $product->get(2)->value());
    }

    public function testGetRow()
    {
        $row = $this->matrix->getRow_(2);
        $this->assertEquals('[ 4 ; -5 ; 666 ]', (string) $row);
    }

    public function testGetCol()
    {
        $row = $this->matrix->getCol_(2);
        $this->assertEquals('[ 1/2 ; -5 ]', (string) $row);
    }

    //TODO: testDimensionExceptions()
}