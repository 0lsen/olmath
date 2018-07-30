<?php

namespace Math\Number\Model;

use Math\Number\Exception\UnknownOperatorException;

/**
 * Class AbstractNumber
 * @method Number add_(Number $number)
 * @method Number subtract_(Number $number)
 * @method Number multiplyWith_(Number $number)
 * @method Number divideBy_(Number $number)
 * @method Number negative_()
 * @method Number square_()
 */
abstract class AbstractNumber implements Number
{
    /**
     * @param $name
     * @param $arguments
     * @return Number
     * @throws UnknownOperatorException
     */
    public function __call($name, $arguments)
    {
        if (substr($name,-1) == "_") {
            $operator = substr($name, 0, -1);
            if (is_callable([$this, $operator])) {
                $clone = clone $this;
                return call_user_func([$clone, $operator], $arguments[0] ?? null);
            } else {
                throw new UnknownOperatorException($operator);
            }
        } else {
            throw new UnknownOperatorException($name);
        }
    }
}