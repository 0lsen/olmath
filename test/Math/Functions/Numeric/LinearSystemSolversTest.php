<?php

use Math\Functions\Numeric\LeastSquaresSolvers;
use Math\Model\Matrix\Matrix;
use Math\Model\Matrix\SparseInput\SingleElement;
use Math\Model\Matrix\SparseMatrix;
use Math\Model\Number\RationalNumber;
use Math\Model\Vector\Vector;
use PHPUnit\Framework\TestCase;

class LinearSystemSolversTest extends TestCase
{
    public function testLsmr()
    {
        // Most simple solvable example --------------------------------------------------------------------------------

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

        $x = LeastSquaresSolvers::LSMR($A, $b);

        $this->assertEquals(6, $x->getDim());
        $this->assertEquals(1, $x->get(1)->value());
        $this->assertEquals(2, $x->get(2)->value());
        $this->assertEquals(3, $x->get(3)->value());
        $this->assertEquals(4, $x->get(4)->value());

        $min = -5; $max = 5;
        $m = 10; $n = 5;

        // random (not at all) sparse rectangular matrix with right side calculated from random "solution"  ------------

        $entries = [];
        for ($i = 0; $i < $m; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $entries[] = new SingleElement($i+1, $j+1, $this->randomIntNumber($min, $max));
            }
        }
        $A = new SparseMatrix($m, $n, ...$entries);

        $entries = [];
        for ($i = 0; $i < $n; $i++) {
            $entries[] = $this->randomIntNumber($min, $max);
        }

        $x = new Vector(...$entries);
        $b = $A->multiplyWithVector($x);

        $xCalc = LeastSquaresSolvers::LSMR($A, $b);

        for ($i = 0; $i < $n; $i++) {
            $this->assertEquals($x->get($i+1)->value(), $xCalc->get($i+1)->value());
        }

        // random full rectangular matrix with right side calculated from random "solution"  ---------------------------

        $rows = [];
        for ($i = 0; $i < $m; $i++) {
            $row = [];
            for ($j = 0; $j < $n; $j++) {
                $row[] = $this->randomIntNumber($min, $max);
            }
            $rows[] = $row;
        }

        $entries = [];
        for ($i = 0; $i < $n; $i++) {
            $entries[] = $this->randomIntNumber($min, $max);
        }

        $A = new Matrix(...$rows);
        $x = new Vector(...$entries);
        $b = $A->multiplyWithVector($x);

        $xCalc = LeastSquaresSolvers::LSMR($A, $b);

        for ($i = 0; $i < $n; $i++) {
            $this->assertEquals($x->get($i+1)->value(), $xCalc->get($i+1)->value());
        }

        // ToDo: test least squares problem with no exact solution
    }

    public function testLsqr()
    {
        // Most simple solvable example --------------------------------------------------------------------------------

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

        $x = LeastSquaresSolvers::LSQR($A, $b);

        $this->assertEquals(4, $x->getDim());
        $this->assertEquals(1, $x->get(1)->value());
        $this->assertEquals(2, $x->get(2)->value());
        $this->assertEquals(3, $x->get(3)->value());
        $this->assertEquals(4, $x->get(4)->value());

        $min = -5; $max = 5;
        $m = 10; $n = 5;

        // random (not at all) sparse rectangular matrix with right side calculated from random "solution"  ------------

        $entries = [];
        for ($i = 0; $i < $m; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $entries[] = new SingleElement($i+1, $j+1, $this->randomIntNumber($min, $max));
            }
        }
        $A = new SparseMatrix($m, $n, ...$entries);

        $entries = [];
        for ($i = 0; $i < $n; $i++) {
            $entries[] = $this->randomIntNumber($min, $max);
        }

        $x = new Vector(...$entries);
        $b = $A->multiplyWithVector($x);

        $xCalc = LeastSquaresSolvers::LSMR($A, $b);

        for ($i = 0; $i < $n; $i++) {
            $this->assertEquals($x->get($i+1)->value(), $xCalc->get($i+1)->value());
        }

        // random full rectangular matrix with right side calculated from random "solution"  ---------------------------

        $rows = [];
        for ($i = 0; $i < $m; $i++) {
            $row = [];
            for ($j = 0; $j < $n; $j++) {
                $row[] = $this->randomIntNumber($min, $max);
            }
            $rows[] = $row;
        }

        $entries = [];
        for ($i = 0; $i < $n; $i++) {
            $entries[] = $this->randomIntNumber($min, $max);
        }

        $A = new Matrix(...$rows);
        $x = new Vector(...$entries);
        $b = $A->multiplyWithVector($x);

        $xCalc = LeastSquaresSolvers::LSMR($A, $b);

        for ($i = 0; $i < $n; $i++) {
            $this->assertEquals($x->get($i+1)->value(), $xCalc->get($i+1)->value());
        }

        // ToDo: test problems with no exact solution
    }

    private function randomIntNumber(int $min, int $max)
    {
        $int = rand($min, $max);
        return new RationalNumber($int, 1, $int <=> 0);
    }
}
