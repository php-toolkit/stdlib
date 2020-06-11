<?php declare(strict_types=1);

namespace Toolkit\Stdlib\Obj\Traits;

/**
 * Trait CreateSingletonTrait
 *
 * @package Toolkit\Stdlib\Obj\Traits
 */
trait CreateSingletonTrait
{
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

}
