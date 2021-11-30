<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Obj\Traits;

/**
 * Trait SingletonPoolTrait
 *
 * @package Toolkit\Stdlib\Obj\Traits
 */
trait SingletonPoolTrait
{
    /**
     * @var array
     */
    private static array $singletons = [];

    /**
     * @param string $class
     *
     * @return mixed
     */
    public static function singleton(string $class): mixed
    {
        if (!isset(self::$singletons[$class])) {
            self::$singletons[$class] = new $class;
        }

        return self::$singletons[$class];
    }

    /**
     * @param string $class
     *
     * @return mixed
     */
    public static function factory(string $class): mixed
    {
        if (!isset(self::$singletons[$class])) {
            self::$singletons[$class] = new $class;
        }

        return clone self::$singletons[$class];
    }
}
