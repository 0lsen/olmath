<?php

namespace Api;


use Math\Model\Number\ComplexNumber;
use Math\Model\Number\Number;
use Math\Model\Number\RationalNumber;
use Math\Model\Number\RealNumber;
use Math\Model\Number\Zero;
use Math\Parser\FormulaResult;

class Mapper
{
    /**
     * @param FormulaResult $result
     * @return \Swagger\Client\Model\FormulaResult
     * @throws \Exception
     */
    public static function mapNumberResults(FormulaResult $result)
    {
        $apiResult = new \Swagger\Client\Model\FormulaResult();
        $entries = [];
        foreach ($result->getEntries() as $index => $entry) {
            $apiEntry = new \Swagger\Client\Model\FormulaResultEntry();
            $apiEntry->setOriginal($entry->getOriginal());
            $apiEntry->setDbz($entry->isDbz());
            $apiEntry->setVariable($entry->getVariable());
            if (!$entry->isDbz()) $apiEntry->setResult(self::mapNumberToApi($entry->getResult()));
            $entries[$index] = $apiEntry;
        }
        $apiResult->setEntries($entries);

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