<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Obj\Traits;

use Toolkit\Stdlib\Obj;

/**
 * Trait SingletonTrait
 *
 * @package Toolkit\Stdlib\Obj\Traits
 */
trait SingletonTrait
{
    /**
     * @return mixed
     */
    public static function new(): mixed
    {
        return Obj::singleton(static::class);
    }

    private function __clone()
    {
    }
}
