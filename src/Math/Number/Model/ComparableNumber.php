<?php

namespace Math\Number\Model;

interface ComparableNumber extends Number
{
    /**
     * @param Number $number
     * @return integer
     */
    public function compareTo(ComparableNumber $number);
}