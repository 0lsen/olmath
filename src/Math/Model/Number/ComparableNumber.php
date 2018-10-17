<?php

namespace Math\Model\Number;

interface ComparableNumber extends Number
{
    /**
     * @param Number $number
     * @return integer
     */
    public function compareTo(ComparableNumber $number);

    /**
     * @return Number
     */
    public function squareRoot();

    /**
     * @param int $nth
     * @return Number
     */
    public function root($nth);
}