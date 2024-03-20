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
 */
trait SingletonTrait
{
    /**
     * Alias of instance.
     *
     * @return static
     */
    public static function new(): static
    {
        return Obj::singleton(static::class);
    }

    /**
     * Get singleton instance of the class.
     *
     * @return static
     */
    public static function instance(): static
    {
        return Obj::singleton(static::class);
    }

    private function __clone()
    {
    }
}
