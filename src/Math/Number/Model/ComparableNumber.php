<?php

namespace Math\Number\Model;

interface ComparableNumber
{
    /**
     * @param Number $number
     * @return integer
     */
    public function compareTo(Number $number);
}