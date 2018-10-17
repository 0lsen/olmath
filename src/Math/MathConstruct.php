<?php

namespace Math;


use Math\Exception\UnknownOperatorException;

abstract class MathConstruct
{
    /**
     * @param $name
     * @param $arguments
     * @return MathInterface
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