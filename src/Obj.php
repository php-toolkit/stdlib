<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Obj;

use ArrayAccess;
use Toolkit\Stdlib\Obj\Traits\ObjectPoolTrait;

/**
 * Class Obj
 *  alias of the ObjectHelper
 *
 * @package Toolkit\Stdlib\Obj
 */
class Obj extends ObjectHelper
{
    use ObjectPoolTrait;

    /**
     * @var array
     */
    private static $singletons = [];

    /**
     * @param string $class
     *
     * @return mixed
     */
    public static function singleton(string $class)
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
    public static function factory(string $class)
    {
        if (!isset(self::$singletons[$class])) {
            self::$singletons[$class] = new $class;
        }

        return clone self::$singletons[$class];
    }

    /**
     * @param $object
     *
     * @return bool
     */
    public static function isArrayable($object): bool
    {
        return $object instanceof ArrayAccess || method_exists($object, 'toArray');
    }
}
