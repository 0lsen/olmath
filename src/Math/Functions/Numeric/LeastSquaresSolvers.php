<?php

namespace Math\Functions\Numeric;


use Math\Exception\DimensionException;
use Math\Model\Matrix\MatrixInterface;
use Math\Model\Number\ComparableNumber;
use Math\Model\Number\RationalNumber;
use Math\Model\Number\Zero;
use Math\Model\Vector\SparseVector;
use Math\Model\Vector\VectorInterface;

class LeastSquaresSolvers
{
    /**
     * Least Squares Minimal Residual
     * http://web.stanford.edu/group/SOL/software/lsmr/LSMR-SISC-2011.pdf
     *
     * @param MatrixInterface $A
     * @param VectorInterface $b
     * @param int|null $maxIt
     * @param ComparableNumber|null $atol
     * @param ComparableNumber|null $btol
     * @param ComparableNumber|null $lambda
     * @return VectorInterface
     * @throws DimensionException
     * @throws \Math\Exception\DivisionByZeroException
     * @throws \Math\Exception\UnknownOperandException
     * @todo reorthogonalisation
     */
    public static function LSMR(
        MatrixInterface $A,
        VectorInterface $b,
        int $maxIt = null,
        ComparableNumber $atol = null,
        ComparableNumber $btol = null,
        ComparableNumber $lambda = null
    )
    {
        list($m,$n) = $A->getDims();
        if ($m != $b->getDim()) {
            throw new DimensionException('matrix ('.$m.','.$n.') does not match vector of size '.$b->getDim());
        }

        if (is_null($maxIt)) $maxIt = 100;
        if (is_null($atol)) $atol = new RationalNumber(1, 100000000, 1);
        if (is_null($btol)) $btol = new RationalNumber(1, 100000000, 1);

        if (!is_null($lambda)) $rd = Zero::getInstance();

        $it = 0;
        $x = new SparseVector($n);

        $beta = $b->norm();
        $u = $b->normalise_();
        $bNorm = clone $beta;

        $v = $A->multiplyWithVector($u, true);
        $alpha = $v->norm();
        $v = $v->normalise();

        $tau = clone $alpha;
        $rhoOld = new RationalNumber(1);
        $rhoOldBar = new RationalNumber(1);
        $zetaBar = $alpha->multiplyWith_($beta);

        $cBar = new RationalNumber(1);
        $sBar = Zero::getInstance();
        $h = clone $v;
        $hBar = new SparseVector($n);

        $betaDoubleDot = clone $beta;
        $betaDot = Zero::getInstance();
        $rhoDot = new RationalNumber(1);
        $tauTilde = Zero::getInstance();
        $thetaTildeOld = Zero::getInstance();
        $zetaOld = Zero::getInstance();

        $aNormSquared = $alpha->normSquared();

        if ($beta->value() == 0) {
            return new SparseVector($n);
        }

        while (++$it <= $maxIt) {
            $u = $A->multiplyWithVector($v)->addVector($u->multiplyWithScalar_($alpha->negative_()));
            $beta = $u->norm();
            $u = $u->normalise();

            $v = $A->multiplyWithVector($u, true)->addVector($v->multiplyWithScalar_($beta->negative_()));
            $alpha = $v->norm();
            $v = $v->normalise();

            if (is_null($lambda)) {
                /** @var ComparableNumber $rho */
                $rho = $tau->normSquared()->add($beta->normSquared())->squareRoot();
                $c = $tau->divideBy_($rho);
            } else {
                /** @var ComparableNumber $rhoHat */
                $rhoHat = $tau->normSquared()->add($lambda->normSquared())->squareRoot();
                $cHat = $tau->divideBy_($rhoHat);
                $sHat = $lambda->divideBy_($rhoHat);
                /** @var ComparableNumber $rho */
                $rho = $rhoHat->normSquared()->add($beta->normSquared())->squareRoot();
                $c = $rhoHat->divideBy_($rho);
            }

            $s = $beta->negative_()->divideBy($rho);
            $theta = $s->negative_()->multiplyWith($alpha);
            $tau = $c->multiplyWith_($alpha);

            $thetaBar = $sBar->negative_()->multiplyWith($rho);
            $rhoBar = $cBar->multiplyWith_($rho)->square()->add($theta->square_())->squareRoot();
            $cBar = $cBar->multiplyWith($rho->divideBy_($rhoBar));
            $sBar = $theta->negative_()->divideBy($rhoBar);

            $zeta = $cBar->multiplyWith_($zetaBar);
            $zetaBar = $zetaBar->multiplyWith($sBar);

            $hBar = $h->addVector_($hBar->multiplyWithScalar($thetaBar->multiplyWith_($rho)->negative()->divideBy($rhoOld->multiplyWith_($rhoOldBar))));
            $x = $x->addVector($hBar->multiplyWithScalar_($zeta->divideBy_($rho->multiplyWith_($rhoBar))));
            $h = $v->addVector_($h->multiplyWithScalar_($theta->negative_()->divideBy($rho)));

            if (is_null($lambda)) {
                $betaHat = $c->multiplyWith_($betaDoubleDot);
                $betaDoubleDot = $betaDoubleDot->multiplyWith($s);
            } else {
                $betaCheck = $sHat->multiplyWith_($betaDoubleDot);
                $betaHat = $c->multiplyWith_($cHat)->multiplyWith($betaDoubleDot);
                $betaDoubleDot = $betaDoubleDot->multiplyWith($s)->multiplyWith($cHat);
            }

            $rhoTilde = $rhoDot->normSquared()->add($thetaBar->normSquared())->squareRoot();
            $cTilde = $rhoDot->divideBy_($rhoTilde);
            $sTilde = $thetaBar->negative_()->divideBy($rhoTilde);
            $thetaTilde = $sTilde->negative_()->multiplyWith($rhoBar);
            $rhoDot = $cTilde->multiplyWith_($rhoBar);
            $betaDot = $betaDot->multiplyWith($sTilde)->add($betaHat->multiplyWith_($cTilde));
            $tauTilde = $zetaOld->subtract_($thetaTildeOld->multiplyWith_($tauTilde))->divideBy($rhoTilde);
            $tauDot = $zeta->subtract_($thetaTilde->multiplyWith_($tauTilde))->divideBy($rhoDot);

            if (is_null($lambda)) {
                /** @var ComparableNumber $r */
                $r = $betaDot->subtract_($tauDot)->normSquared()->add($betaDoubleDot->normSquared())->squareRoot();
            } else {
                $rd = $rd->add($betaCheck->normSquared());
                /** @var ComparableNumber $r */
                $r = $rd->add_($betaDot->subtract_($tauDot)->normSquared()->add($betaDoubleDot->normSquared()))->squareRoot();
            }

            $aTr = $zetaBar->absoluteValue();
            $xNorm = $x->norm();

            $aNormSquared = $aNormSquared->add($beta->normSquared());
            if (!is_null($lambda)){
                $aNormSquared = $aNormSquared->add($lambda->normSquared());
            }
            $aNorm = $aNormSquared->squareRoot();
            $aNormSquared = $aNormSquared->add($alpha->normSquared());

            $rhoOld = $rho;
            $rhoOldBar = $rhoBar;
            $zetaOld = $zeta;
            $thetaTildeOld = $thetaTilde;

            if (
                $r->value() < $btol->multiplyWith_($bNorm)->add($atol->multiplyWith_($aNorm)->multiplyWith($xNorm))->value()
                || $aTr < $atol->multiplyWith_($aNorm)->multiplyWith($r)->value()
            ) {
                break;
            }
        }

        return $x;
    }

    /**
     * Least Squares QR
     * http://web.stanford.edu/group/SOL/software/lsqr/lsqr-toms82a.pdf
     *
     * @param MatrixInterface $A
     * @param VectorInterface $b
     * @param int|null $maxIt
     * @param ComparableNumber|null $atol
     * @param ComparableNumber|null $btol
     * @param ComparableNumber|null $lambda
     * @return SparseVector|VectorInterface
     * @throws DimensionException
     * @throws \Math\Exception\DivisionByZeroException
     * @throws \Math\Exception\UnknownOperandException
     * @todo reorthogonalisation
     */
    public static function LSQR(
        MatrixInterface $A,
        VectorInterface $b,
        int $maxIt = null,
        ComparableNumber $atol = null,
        ComparableNumber $btol = null,
        ComparableNumber $lambda = null
    )
    {
        list($m,$n) = $A->getDims();
        if ($m != $b->getDim()) {
            throw new DimensionException('matrix ('.$m.','.$n.') does not match vector of size '.$b->getDim());
        }

        if (is_null($maxIt)) $maxIt = 100;
        if (is_null($atol)) $atol = new RationalNumber(1, 1000000, 1);
        if (is_null($btol)) $btol = new RationalNumber(1, 1000000, 1);

        if (!is_null($lambda)) $rd = Zero::getInstance();

        $it = 0;
        $x = new SparseVector($n);

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

        while (++$it <= $maxIt) {
            $u = $A->multiplyWithVector($v)->addVector($u->multiplyWithScalar_($alpha->negative_()));
            $beta = $u->norm();
            $u = $u->normalise();

            $v = $A->multiplyWithVector($u, true)->addVector($v->multiplyWithScalar_($beta->negative_()));
            $alpha = $v->norm();
            $v = $v->normalise();

            if (is_null($lambda)) {
                /** @var ComparableNumber $rho */
                $rho = $tau->normSquared()->add($beta->normSquared())->squareRoot();
                $c = $tau->divideBy_($rho);
            } else {
                /** @var ComparableNumber $rhoHat */
                $rhoHat = $tau->normSquared()->add($lambda->normSquared())->squareRoot();
                $cHat = $tau->divideBy_($rhoHat);
                $sHat = $lambda->negative_()->divideBy($rhoHat);
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

            if (is_null($lambda)) {
                $r = $phiBar->absoluteValue();
            } else {
                $rd = $rd->add($psi->normSquared());
                /** @var ComparableNumber $r */
                $r = $rd->add_($phiBar->normSquared())->squareRoot()->absoluteValue();
            }

            $aTr = $alpha->multiplyWith_($c)->multiplyWith($phiBar)->absoluteValue();
            $xNorm = $x->norm();

            $aNormSquared = $aNormSquared->add($beta->normSquared());
            if (!is_null($lambda)){
                $aNormSquared = $aNormSquared->add($lambda->normSquared());
            }
            $aNorm = $aNormSquared->squareRoot();
            $aNormSquared = $aNormSquared->add($alpha->normSquared());

            if (
                $r < $btol->multiplyWith_($bNorm)->add($atol->multiplyWith_($aNorm)->multiplyWith($xNorm))->value()
                || $aTr < $atol->multiplyWith_($aNorm)->multiplyWith($r)->value()
            ) {
                break;
            }
        }

        return $x;
    }
}