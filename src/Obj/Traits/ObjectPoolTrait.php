<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Obj\Traits;

use Closure;
use InvalidArgumentException;
use SplStack;
use stdClass;
use function count;
use function get_class;
use function is_string;

/**
 * Class ObjectPoolTrait
 *
 * @package Toolkit\Stdlib\Obj\Traits
 */
trait ObjectPoolTrait
{
    /**
     * @var SplStack[] [class => \SplStack]
     */
    private static array $pool = [];

    /**
     * @param string $class
     *
     * @return mixed
     */
    public static function get(string $class): mixed
    {
        $stack = self::getStack($class);

        if (!$stack->isEmpty()) {
            return $stack->shift();
        }

        return new $class;
    }

    /**
     * @param string|stdClass $object
     */
    public static function put(string|stdClass $object): void
    {
        if (is_string($object)) {
            $object = new $object;
        }

        self::getStack($object)->push($object);
    }

    /**
     * @param string $class
     * @param Closure $handler
     *
     * @return mixed
     */
    public static function use(string $class, Closure $handler): mixed
    {
        $obj = self::get($class);
        $ret = $handler($obj);

        self::put($obj);
        return $ret;
    }

    /**
     * @param string|stdClass $class
     *
     * @return SplStack
     */
    public static function getStack(string|stdClass $class): SplStack
    {
        $class = is_string($class) ? $class : get_class($class);

        if (!isset(self::$pool[$class])) {
            self::$pool[$class] = new SplStack();
        }

        return self::$pool[$class];
    }

    /**
     * @param null $class
     *
     * @return int
     * @throws InvalidArgumentException
     */
    public static function count($class = null): int
    {
        if ($class) {
            if (!isset(self::$pool[$class])) {
                throw new InvalidArgumentException("The object is never created of the class: $class");
            }

            return self::$pool[$class]->count();
        }

        return count(self::$pool);
    }

    /**
     * @param null $class
     *
     * @throws InvalidArgumentException
     */
    public static function destroy($class = null): void
    {
        if ($class) {
            if (!isset(self::$pool[$class])) {
                throw new InvalidArgumentException("The object is never created of the class: $class");
            }

            unset(self::$pool[$class]);
        } else {
            self::$pool = [];
        }
    }
}
