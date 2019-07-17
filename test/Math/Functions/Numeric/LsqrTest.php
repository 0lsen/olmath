<?php

use Math\Functions\Numeric\LeastSquaresSolvers;
use Math\Model\Matrix\Matrix;
use Math\Model\Matrix\SparseInput\SingleElement;
use Math\Model\Matrix\SparseMatrix;
use Math\Model\Number\RationalNumber;
use Math\Model\Vector\Vector;
use PHPUnit\Framework\TestCase;

class LsqrTest extends TestCase
{
    private $min = -5;
    private $max = 5;
    private $m = 10;
    private $n = 5;

    public function testTrivialIdentityMatrix()
    {
        $A = new SparseMatrix(2, 2,
            new SingleElement(1, 1, new RationalNumber(1)),
            new SingleElement(2, 2, new RationalNumber(1))
        );

        $b = new Vector(
            new RationalNumber(1),
            new RationalNumber(2)
        );

        $x = LeastSquaresSolvers::LSMR($A, $b);

        $this->assertEquals(2, $x->getDim());
        $this->assertEquals(1, $x->get(1)->value());
        $this->assertEquals(2, $x->get(2)->value());
    }

    public function testTrivialSolvable()
    {
        $A = new SparseMatrix(4,6,
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

        $x = LeastSquaresSolvers::LSQR($A, $b);

        $this->assertEquals(6, $x->getDim());
        $this->assertEquals(1, $x->get(1)->value());
        $this->assertEquals(2, $x->get(2)->value());
        $this->assertEquals(3, $x->get(3)->value());
        $this->assertEquals(4, $x->get(4)->value());
        $this->assertEquals(0, $x->get(5)->value());
        $this->assertEquals(0, $x->get(6)->value());
    }

    public function testRandomSparseSolvable()
    {
        $entries = [];
        for ($i = 0; $i < $this->m; $i++) {
            for ($j = 0; $j < $this->n; $j++) {
                $entries[] = new SingleElement($i+1, $j+1, $this->randomIntNumber($this->min, $this->max));
            }
        }
        $A = new SparseMatrix($this->m, $this->n, ...$entries);

        $entries = [];
        for ($i = 0; $i < $this->n; $i++) {
            $entries[] = $this->randomIntNumber($this->min, $this->max);
        }

        $x = new Vector(...$entries);
        $b = $A->multiplyWithVector($x);

        $xCalc = LeastSquaresSolvers::LSQR($A, $b);

        for ($i = 0; $i < $this->n; $i++) {
            $this->assertEquals($x->get($i+1)->value(), $xCalc->get($i+1)->value());
        }
    }

    public function testRandomFullSolvable()
    {
        $rows = [];
        for ($i = 0; $i < $this->m; $i++) {
            $row = [];
            for ($j = 0; $j < $this->n; $j++) {
                $row[] = $this->randomIntNumber($this->min, $this->max);
            }
            $rows[] = $row;
        }

        $entries = [];
        for ($i = 0; $i < $this->n; $i++) {
            $entries[] = $this->randomIntNumber($this->min, $this->max);
        }

        $A = new Matrix(...$rows);
        $x = new Vector(...$entries);
        $b = $A->multiplyWithVector($x);

        $xCalc = LeastSquaresSolvers::LSQR($A, $b);

        for ($i = 0; $i < $this->n; $i++) {
            $this->assertEquals($x->get($i+1)->value(), $xCalc->get($i+1)->value());
        }
    }

    // ToDo: test problems with no exact solution

    private function randomIntNumber(int $min, int $max)
    {
        $int = rand($min, $max);
        return new RationalNumber($int, 1, $int <=> 0);
    }
}
