<?php

use Math\Model\Matrix\Matrix;
use Math\Model\Matrix\MatrixInterface;
use Math\Model\Number\RealNumber;
use PHPUnit\Framework\TestCase;

class MatrixTest extends TestCase
{
    /** @var MatrixInterface */
    private $matrix;

    public function setUp()
    {
        $this->matrix = new Matrix(
            [new RealNumber(1), new RealNumber(2), new RealNumber(3)],
            [new RealNumber(4), new RealNumber(-5), new RealNumber(666)]
        );
        parent::setUp();
    }

    public function testToString()
    {
        $this->assertEquals("[ 1 |  2 |   3 ]\n[ 4 | -5 | 666 ]", (string) $this->matrix);
    }

    public function testTranspose()
    {
        $this->assertEquals("[ 1 |   4 ]\n[ 2 |  -5 ]\n[ 3 | 666 ]", (string) $this->matrix->transpose_());
    }

    //TODO: testMultiplyWithScalar()
    //TODO: testMultiplyWithVector()
    //TODO: testDimensionExceptions()
}