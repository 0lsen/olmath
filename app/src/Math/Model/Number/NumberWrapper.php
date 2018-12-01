<?php

namespace Math\Model\Number;

use Math\MathConstruct;

/**
 * Class NumberWrapper
 * @package Math\Model\Number
 * @method int|float|double|ComplexNumber value()
 * @method \Math\Model\Number\Number add(\Math\Model\Number\Number $number)
 * @method \Math\Model\Number\Number add_(\Math\Model\Number\Number $number)
 * @method \Math\Model\Number\Number subtract(\Math\Model\Number\Number $number)
 * @method \Math\Model\Number\Number subtract_(\Math\Model\Number\Number $number)
 * @method \Math\Model\Number\Number multiplyWith(\Math\Model\Number\Number $number)
 * @method \Math\Model\Number\Number multiplyWith_(\Math\Model\Number\Number $number)
 * @method \Math\Model\Number\Number divideBy(\Math\Model\Number\Number $number)
 * @method \Math\Model\Number\Number divideBy_(\Math\Model\Number\Number $number)
 * @method \Math\Model\Number\Number negative()
 * @method \Math\Model\Number\Number negative_()
 * @method \Math\Model\Number\Number square()
 * @method \Math\Model\Number\Number square_()
 */
class NumberWrapper extends MathConstruct
{
    private $number;

    /**
     * List of functions that return Numbers that do not represent the wrapped Number and therefore must not be stored
     * @var array
     */
    private static $functionBlacklist = [
        'normSquared',
    ];

    /**
     * NumberWrapper constructor.
     * @param Number $number
     */
    public function __construct(Number $number)
    {
        $this->number = $number;
    }

    public function __clone()
    {
        $this->number = clone $this->number;
    }

    public function __toString()
    {
        return $this->number->__toString();
    }

    /**
     * @return Number
     */
    public function get()
    {
        return $this->number;
    }

    public function __call($name, $arguments)
    {
        foreach ($arguments as &$argument) {
            if ($argument instanceof NumberWrapper) {
                $argument = $argument->get();
            }
        }
        $result = call_user_func([$this->number, $name], ...$arguments);
        if (
            $result instanceof Number &&
            $result !== $this->number &&
            substr($name,-1) != "_" &&
            !in_array($name, self::$functionBlacklist)
        ) {
            $this->number = $result;
        }
        return $result;
    }
}