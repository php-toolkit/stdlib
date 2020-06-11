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
use Toolkit\Stdlib\Obj\Traits\CreateSingletonTrait;
use Toolkit\Stdlib\Obj\Traits\ObjectPoolTrait;

/**
 * Class Obj
 *  alias of the ObjectHelper
 *
 * @package Toolkit\Stdlib\Obj
 */
class Obj extends ObjectHelper
{
    use ObjectPoolTrait, CreateSingletonTrait;
}
