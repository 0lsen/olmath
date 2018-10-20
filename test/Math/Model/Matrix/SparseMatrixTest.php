<?php

use Math\Model\Matrix\MatrixInterface;
use Math\Model\Matrix\SparseInput;
use Math\Model\Matrix\SparseMatrix;
use Math\Model\Number\ComplexNumber;
use Math\Model\Number\RationalNumber;
use Math\Model\Number\Zero;
use Math\Model\Vector\SparseVector;
use PHPUnit\Framework\TestCase;

class SparseMatrixTest extends TestCase
{
    /** @var MatrixInterface */
    private $matrix;

    public function setUp()
    {
        $entry1x1 = new SparseInput\SingleElement(1, 1, new RationalNumber(1));
        $entry2x1 = new SparseInput\SingleElement(2, 1, new RationalNumber(5, 1, -1));
        $entry1x2 = new SparseInput\SingleElement(1, 2, new ComplexNumber(new RationalNumber(2), new RationalNumber(1,4,-1)));
        $entry1x3 = new SparseInput\SingleElement(1, 3, new RationalNumber(3));
        $entry5x6 = new SparseInput\SingleElement(10, 5, new ComplexNumber(Zero::getInstance(), new RationalNumber(1)));
        $this->matrix = new SparseMatrix(10, 6,
            $entry1x1,
            $entry2x1,
            $entry1x2,
            $entry1x3,
            $entry5x6
        );
        parent::setUp();
    }

    public function testToString()
    {
        $this->assertEquals(""
                            ."[ 1,1:  1   1,2: 2 - 1/4 i   1,3: 3           ]\n"
                            ."[ 2,1: -5                                     ]\n"
                            ."[                                     10,5: i ]", (string) $this->matrix);
    }

    public function testTranspose()
    {
        $this->assertEquals(""
            ."[ 1,1:         1   1,2: -5           ]\n"
            ."[ 2,1: 2 - 1/4 i                     ]\n"
            ."[ 3,1:         3                     ]\n"
            ."[                            5,10: i ]", (string) $this->matrix->transpose_());
    }

    public function testMultiplyWithScalar()
    {
        $this->assertEquals(""
            ."[ 1,1:   2   1,2: 4 - 1/2 i   1,3: 6             ]\n"
            ."[ 2,1: -10                                       ]\n"
            ."[                                      10,5: 2 i ]", (string) $this->matrix->multiplyWithScalar(new RationalNumber(2)));
    }

    public function testMultiplyWithVector()
    {
        $vector = new SparseVector(6, [
            0 => new RationalNumber(1),
            2 => new RationalNumber(3),
            4 => new RationalNumber(5),
            5 => new RationalNumber(6)
        ]);

        $product = $this->matrix->multiplyWithVector($vector);

        $this->assertEquals(10, $product->getDim());
        $this->assertEquals(10, $product->get(1)->value());
        $this->assertEquals(-5, $product->get(2)->value());
        $this->assertEquals(0, $product->get(3)->value());
        $this->assertEquals(0, $product->get(4)->value());
        $this->assertEquals(0, $product->get(5)->value());
        $this->assertEquals(0, $product->get(6)->value());
        $this->assertEquals(0, $product->get(7)->value());
        $this->assertEquals(0, $product->get(8)->value());
        $this->assertEquals(0, $product->get(9)->value());
        $this->assertEquals(0, $product->get(10)->getR()->value());
        $this->assertEquals(5, $product->get(10)->getI()->value());
    }

    public function testGetRow()
    {
        $row = $this->matrix->getRow_(1);
        $this->assertEquals('[ 1: 1 ; 2: 2 - 1/4 i ; 3: 3 ]', (string) $row);

        $row = $this->matrix->getRow_(2);
        $this->assertEquals('[ 1: -5 ]', (string) $row);

        $row = $this->matrix->getRow_(3);
        $this->assertEquals('[  ]', (string) $row);
    }

    public function testGetCol()
    {
        $row = $this->matrix->getCol_(1);
        $this->assertEquals('[ 1: 1 ; 2: -5 ]', (string) $row);

        $row = $this->matrix->getCol_(2);
        $this->assertEquals('[ 1: 2 - 1/4 i ]', (string) $row);

        $row = $this->matrix->getCol_(4);
        $this->assertEquals('[  ]', (string) $row);
    }

    public function testSetRow()
    {
        $vector = new SparseVector(6, [
            0 => new RationalNumber(7),
            2 => new RationalNumber(8)
        ]);

        $this->assertEquals(""
            ."[ 1,1: 1   1,2: 2 - 1/4 i   1,3: 3           ]\n"
            ."[ 2,1: 7                    2,3: 8           ]\n"
            ."[                                    10,5: i ]", (string) $this->matrix->setRow_(2, $vector));
    }

    public function testSetCol()
    {
        $vector = new SparseVector(10, [
            0 => new RationalNumber(7),
            9 => new RationalNumber(8)
        ]);

        $this->assertEquals(""
            ."[ 1,1:  1   1,2:  7   1,3: 3           ]\n"
            ."[ 2,1: -5                              ]\n"
            ."[           10,2: 8            10,5: i ]", (string) $this->matrix->setCol_(2, $vector));
    }

    //TODO: testDimensionExceptions()
}