<?php

namespace Math\Model\Number;

use Math\MathConstruct;

/**
 * Class AbstractNumber
 * @method \Math\Model\Number\Number add_(\Math\Model\Number\Number $number)
 * @method \Math\Model\Number\Number subtract_(\Math\Model\Number\Number $number)
 * @method \Math\Model\Number\Number multiplyWith_(\Math\Model\Number\Number $number)
 * @method \Math\Model\Number\Number divideBy_(\Math\Model\Number\Number $number)
 * @method \Math\Model\Number\Number negative_()
 * @method \Math\Model\Number\Number square_()
 */
abstract class AbstractNumber extends MathConstruct implements Number {}