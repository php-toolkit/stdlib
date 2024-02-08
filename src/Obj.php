<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib;

use Toolkit\Stdlib\Obj\ObjectHelper;
use Toolkit\Stdlib\Obj\Traits\ObjectPoolTrait;
use Toolkit\Stdlib\Obj\Traits\SingletonPoolTrait;

/**
 * Class Obj
 *  alias of the ObjectHelper
 */
class Obj extends ObjectHelper
{
    use ObjectPoolTrait, SingletonPoolTrait;
}
