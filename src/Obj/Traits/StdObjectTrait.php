<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Obj\Traits;

use InvalidArgumentException;
use Toolkit\Stdlib\Obj;
use function basename;
use function call_user_func_array;
use function dirname;
use function get_class;
use function str_replace;
use function strpos;

/**
 * Class StdObjectTrait
 *
 * @package Toolkit\Stdlib\Obj\Traits
 */
trait StdObjectTrait
{
    /**
     * get called class full name
     *
     * @return string
     */
    final public static function fullName(): string
    {
        return static::class;
    }

    /**
     * get called class namespace
     *
     * @param null|string $fullName
     *
     * @return string
     */
    final public static function spaceName(string $fullName = null): string
    {
        $fullName = $fullName ?: self::fullName();
        $fullName = str_replace('\\', '/', $fullName);

        return strpos($fullName, '/') ? dirname($fullName) : '';
    }

    /**
     * get called class name
     *
     * @param null|string $fullName
     *
     * @return string
     */
    final public static function className(string $fullName = null): string
    {
        $fullName = $fullName ?: self::fullName();
        $fullName = str_replace('\\', '/', $fullName);

        return basename($fullName);
    }

    /**
     * StdObject constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        Obj::init($this, $config);

        $this->init();
    }

    /**
     * init
     */
    protected function init(): void
    {
        // init something ...
    }

    /**
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public function __call(string $method, array $args)
    {
        // if (method_exists($this, $method) && $this->isAllowCall($method) ) {
        //     return call_user_func_array( array($this, $method), (array) $args);
        // }

        throw new InvalidArgumentException('Called a Unknown method: ' . get_class($this) . "->$method()");
    }

    /**
     * @param string $method
     * @param array $args
     *
     * @return mixed
     */
    public static function __callStatic(string $method, array $args)
    {
        if (method_exists(self::class, $method)) {
            return call_user_func_array([self::class, $method], $args);
        }

        throw new InvalidArgumentException('Called a Unknown static method:  ' . self::class . "::$method()");
    }
}
