<?php

namespace Api;


use Math\Model\Number\ComplexNumber;
use Math\Model\Number\Number;
use Math\Model\Number\RationalNumber;
use Math\Model\Number\RealNumber;
use Math\Model\Number\Zero;
use Math\Parser\NumberResult;

class Mapper
{
    public static function mapNumberResult(NumberResult $result)
    {
        $apiResult = new \Swagger\Client\Model\NumberResult();
        $apiResult->setOriginal($result->getOriginal());
        $apiResult->setDbz($result->isDbz());
        $apiResult->setResult(self::mapNumberToApi($result->getResult()));

        return $apiResult;
    }

    /**
     * @return \Swagger\Client\Model\Number
     */
    private static function mapNumberToApi(Number $number)
    {
        if ($number instanceof Zero) {
            return new \Swagger\Client\Model\Zero();
        } elseif ($number instanceof RealNumber) {
            $n = new \Swagger\Client\Model\RealNumber();
            $n->setR($number->getR());
            return $n;
        } elseif ($number instanceof RationalNumber) {
            $n = new \Swagger\Client\Model\RationalNumber();
            $n->setS($number->getS());
            $n->setN($number->getN());
            $n->setD($number->getD());
            return $n;
        } elseif ($number instanceof ComplexNumber) {
            $n = new \Swagger\Client\Model\ComplexNumber();
            $n->setR(self::mapNumberToApi($number->getR()));
            $n->setI(self::mapNumberToApi($number->getI()));
            return $n;
        } else {
            throw new \Exception('Unknown Number type "'.get_class($number).'"');
        }
    }
}