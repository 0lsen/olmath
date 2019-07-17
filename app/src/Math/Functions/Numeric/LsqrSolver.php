<?php

namespace Math\Functions\Numeric;


use Math\Model\Matrix\MatrixInterface;
use Math\Model\Number\ComparableNumber;
use Math\Model\Number\RealNumber;
use Math\Model\Number\Zero;
use Math\Model\Vector\SparseVector;
use Math\Model\Vector\VectorInterface;

class LsqrSolver extends AbstractLeastSquaresSolver
{

    /**
     * Least Squares QR
     * http://web.stanford.edu/group/SOL/software/lsqr/lsqr-toms82a.pdf
     */
    public function solve(MatrixInterface $A, VectorInterface $b)
    {
        $this->setDim($A, $b);

        $trivialSolution = $this->trivialSolution($A, $b);

        if (!is_null($trivialSolution)) return $trivialSolution;

        if (!is_null($this->lambda)) $rd = Zero::getInstance();

        $it = 0;
        $x = new SparseVector($this->n);

        $beta = $b->norm();
        $u = $b->normalise();
        $bNorm = clone $beta;

        $v = $A->multiplyWithVector($u, true);
        $alpha = $v->norm();
        $v = $v->normalise();

        $tau = clone $alpha;
        $phiBar = clone $beta;

        $p = clone $v;

        $aNormSquared = $alpha->normSquared();

        if ($beta->value() == 0) {
            return $x;
        }

        while (++$it <= $this->maxIt) {
            $u = $A->multiplyWithVector($v)->addVector($u->multiplyWithScalar_($alpha->negative_()));
            $beta = $u->norm();
            $u = $u->normalise();

            $v = $A->multiplyWithVector($u, true)->addVector($v->multiplyWithScalar_($beta->negative_()));
            $alpha = $v->norm();
            $v = $v->normalise();

            if (is_null($this->lambda)) {
                /** @var ComparableNumber $rho */
                $rho = $tau->normSquared()->add($beta->normSquared())->squareRoot();
                $c = $tau->divideBy_($rho);
            } else {
                /** @var ComparableNumber $rhoHat */
                $rhoHat = $tau->normSquared()->add($this->lambda->normSquared())->squareRoot();
                $cHat = $tau->divideBy_($rhoHat);
                $sHat = $this->lambda->negative_()->divideBy($rhoHat);
                $psi = $sHat->negative_()->multiplyWith($phiBar);
                $phiBar = $phiBar->multiplyWith($cHat);
                $rho = $rhoHat->normSquared()->add($beta->normSquared())->squareRoot();
                $c = $rhoHat->divideBy_($rho);
            }

            $s = $beta->negative_()->divideBy($rho);
            $theta = $s->negative_()->multiplyWith($alpha);
            $tau = $c->multiplyWith_($alpha);

            $phi = $c->multiplyWith_($phiBar);
            $phiBar = $phiBar->multiplyWith($s);

            $x = $x->addVector($p->multiplyWithScalar_($phi->divideBy_($rho)));
            $p = $v->addVector_($p->multiplyWithScalar($theta->negative_()->divideBy($rho)));

            if (is_null($this->lambda)) {
                $r = $phiBar->absoluteValue();
            } else {
                $rd = $rd->add($psi->normSquared());
                /** @var ComparableNumber $r */
                $r = $rd->add_($phiBar->normSquared())->squareRoot()->absoluteValue();
            }

            $aTr = $alpha->multiplyWith_($c)->multiplyWith($phiBar)->absoluteValue();
            $xNorm = $x->norm();

            $aNormSquared = $aNormSquared->add($beta->normSquared());
            if (!is_null($this->lambda)){
                $aNormSquared = $aNormSquared->add($this->lambda->normSquared());
            }
            $aNorm = $aNormSquared->squareRoot();
            $aNormSquared = $aNormSquared->add($alpha->normSquared());

            if (
                $r < $this->btol->multiplyWith_($bNorm)->add($this->atol->multiplyWith_($aNorm)->multiplyWith($xNorm))->value()
                || $aTr < $this->atol->multiplyWith_($aNorm)->multiplyWith(new RealNumber($r))->value()
            ) {
                break;
            }
        }

        return $x;
    }
}