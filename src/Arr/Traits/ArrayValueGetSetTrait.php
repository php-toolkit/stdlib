<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Arr\Traits;

use ArrayAccess;
use Toolkit\Stdlib\Php;
use Traversable;
use function array_filter;
use function array_shift;
use function count;
use function explode;
use function is_array;
use function is_int;
use function is_object;
use function is_string;
use function trim;

/**
 * Trait ArrayValueGetSetTrait
 *
 * @package Toolkit\Stdlib\Arr\Traits
 */
trait ArrayValueGetSetTrait
{
    /**
     * Add an element to an array using "dot" notation if it doesn't exist.
     *
     * @param array  $array
     * @param string $key
     * @param mixed  $value
     *
     * @return array
     */
    public static function add(array $array, string $key, mixed $value): array
    {
        if (static::has($array, $key)) {
            static::set($array, $key, $value);
        }

        return $array;
    }

    /**
     * Get an item from an array using "dot" notation.
     *
     * @param ArrayAccess|array $array
     * @param string $key
     * @param mixed|null $default
     *
     * @return mixed
     */
    public static function get(ArrayAccess|array $array, string $key, mixed $default = null): mixed
    {
        if (!static::accessible($array)) {
            return Php::value($default);
        }

        if (static::exists($array, $key)) {
            return $array[$key];
        }

        foreach (explode('.', $key) as $segment) {
            if (static::accessible($array) && static::exists($array, $segment)) {
                $array = $array[$segment];
            } else {
                return Php::value($default);
            }
        }

        return $array;
    }

    /**
     * Set an array item to a given value using "dot" notation.
     * If no key is given to the method, the entire array will be replaced.
     *
     * @param array  $array
     * @param string $key
     * @param mixed  $value
     *
     * @return array
     */
    public static function set(array &$array, string $key, mixed $value): array
    {
        // if (null === $key) {
        //     return ($array = $value);
        // }

        $keys = explode('.', $key);
        while (count($keys) > 1) {
            $key = (string)array_shift($keys);
            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if (!isset($array[$key]) || !is_array($array[$key])) {
                $array[$key] = [];
            }

            $array = &$array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }

    /**
     * Get Multi - 获取多个, 可以设置默认值
     *
     * @param array      $data array data
     * @param array      $needKeys
     *                         $needKeys = [
     *                         'name',
     *                         'password',
     *                         'status' => '1'
     *                         ]
     * @param bool|false $unsetKey
     *
     * @return array
     */
    public static function gets(array &$data, array $needKeys = [], bool $unsetKey = false): array
    {
        $needed = [];

        foreach ($needKeys as $key => $default) {
            if (is_int($key)) {
                $key     = $default;
                $default = null;
            }

            if (isset($data[$key])) {
                $value = $data[$key];

                if (is_int($default)) {
                    $value = (int)$value;
                } elseif (is_string($default)) {
                    $value = trim($value);
                } elseif (is_array($default)) {
                    $value = (array)$value;
                }

                $needed[$key] = $value;

                if ($unsetKey) {
                    unset($data[$key]);
                }
            } else {
                $needed[$key] = $default;
            }
        }

        return $needed;
    }

    /**
     * Get data from array or object by path.
     * Example: `DataCollector::getByPath($array, 'foo.bar.yoo')` equals to $array['foo']['bar']['yoo'].
     *
     * @param Traversable|array $data      An array or object to get value.
     * @param string            $path      The key path.
     * @param mixed|null $default
     * @param string            $separator Separator of paths.
     *
     * @return mixed Found value, null if not exists.
     */
    public static function getByPath(Traversable|array $data, string $path, mixed $default = null, string $separator = '.'): mixed
    {
        if (isset($data[$path])) {
            return $data[$path];
        }

        // Error: will clear '0'. eg 'some-key.0'
        // if (!$nodes = array_filter(explode($separator, $path))) {
        if (!$nodes = explode($separator, $path)) {
            return $default;
        }

        $dataTmp = $data;
        foreach ($nodes as $arg) {
            if ((is_array($dataTmp) || $dataTmp instanceof ArrayAccess) && isset($dataTmp[$arg])) {
                $dataTmp = $dataTmp[$arg];
            } elseif (is_object($dataTmp) && isset($dataTmp->$arg)) {
                $dataTmp = $dataTmp->$arg;
            } else {
                return $default;
            }
        }

        return $dataTmp;
    }

    /**
     * find Value By Nodes
     *
     * @param array $data
     * @param array $nodes
     * @param mixed|null $default
     *
     * @return mixed
     */
    public static function getValueByNodes(array $data, array $nodes, mixed $default = null): mixed
    {
        $temp = $data;

        foreach ($nodes as $name) {
            if (isset($temp[$name])) {
                $temp = $temp[$name];
            } else {
                $temp = $default;
                break;
            }
        }

        return $temp;
    }

    /**
     * setByPath
     *
     * @param ArrayAccess|array &$data
     * @param string             $path
     * @param mixed              $value
     * @param string             $separator
     */
    public static function setByPath(ArrayAccess|array &$data, string $path, mixed $value, string $separator = '.'): void
    {
        if (!str_contains($path, $separator)) {
            $data[$path] = $value;
            return;
        }

        if (!$nodes = array_filter(explode($separator, $path))) {
            return;
        }

        $dataTmp = &$data;
        foreach ($nodes as $node) {
            if (is_array($dataTmp)) {
                if (empty($dataTmp[$node])) {
                    $dataTmp[$node] = [];
                }

                $dataTmp = &$dataTmp[$node];
            } else {
                // If a node is value but path is not go to the end, we replace this value as a new store.
                // Then next node can insert new value to this store.
                $dataTmp = [];
            }
        }

        // Now, path go to the end, means we get latest node, set value to this node.
        $dataTmp = $value;
    }
}
