<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Arr;

use ArrayAccess;
use Toolkit\Stdlib\Helper\DataHelper;
use Traversable;
use function array_filter;
use function array_key_exists;
use function array_keys;
use function array_map;
use function array_merge;
use function array_reduce;
use function array_shift;
use function array_values;
use function array_walk_recursive;
use function count;
use function explode;
use function implode;
use function is_array;
use function is_numeric;
use function is_object;
use function mb_strlen;
use function method_exists;
use function trim;

/**
 * Class ArrayHelper
 *
 * @package Toolkit\Stdlib\Arr
 */
class ArrayHelper
{
    use Traits\ArrayCheckTrait;
    use Traits\ArrayConvertTrait;
    use Traits\ArrayMergeTrait;
    use Traits\ArrayValueGetSetTrait;

    /**
     * 清理数组值的空白
     *
     * @param array $data
     *
     * @return array
     */
    public static function valueTrim(array $data): array
    {
        array_walk_recursive($data, static function (&$value): void {
            $value = trim($value);
        });

        return $data;
    }

    /**
     * get key Max Width
     *
     * ```php
     * $data = [
     *     'key1'      => 'value1',
     *     'key2-test' => 'value2',
     * ]
     * ```
     *
     * @param array $data
     * @param bool $excludeInt
     *
     * @return int
     */
    public static function getKeyMaxWidth(array $data, bool $excludeInt = true): int
    {
        $maxWidth = 0;
        foreach ($data as $key => $value) {
            // key is not a integer
            if (!$excludeInt || !is_numeric($key)) {
                $width    = mb_strlen((string)$key, 'UTF-8');
                $maxWidth = $width > $maxWidth ? $width : $maxWidth;
            }
        }

        return $maxWidth;
    }

    /**
     * get max width
     *
     * ```php
     * $keys = [
     *     'key1',
     *     'key2-test',
     * ]
     * ```
     *
     * @param array $keys
     * @param bool $excludeInt
     *
     * @return int
     */
    public static function getMaxWidth(array $keys, bool $excludeInt = true): int
    {
        $maxWidth = 0;
        foreach ($keys as $key) {
            // key is not a integer
            if (!$excludeInt || !is_numeric($key)) {
                $keyWidth = mb_strlen((string)$key, 'UTF-8');
                $maxWidth = $keyWidth > $maxWidth ? $keyWidth : $maxWidth;
            }
        }

        return $maxWidth;
    }

    ////////////////////////////////////////////////////////////
    /// from laravel
    ////////////////////////////////////////////////////////////

    /**
     * Collapse an array of arrays into a single array.
     *
     * @param array $array
     *
     * @return array
     */
    public static function collapse(array $array): array
    {
        $results = [];

        foreach ($array as $values) {
            if (is_object($values) && method_exists($values, 'toArray')) {
                $values = $values->toArray();
            } elseif (!is_array($values)) {
                continue;
            }

            // $results = \array_merge($results, $values);
            $results[] = $values;
        }

        return array_merge(...$results);
    }

    /**
     * like implode() but support any type
     *
     * @param array $list
     * @param string $sep
     *
     * @return string
     */
    public static function join(array $list, string $sep = ','): string
    {
        $strings = [];
        foreach ($list as $value) {
            $strings[] = DataHelper::toString($value, true);
        }

        return implode($sep, $strings);
    }

    /**
     * Cross join the given arrays, returning all possible permutations.
     *
     * @param array ...$arrays
     *
     * @return array
     */
    public static function crossJoin(...$arrays): array
    {
        return array_reduce($arrays, static function ($results, $array) {
            return static::collapse(array_map(static function ($parent) use ($array) {
                return array_map(static function ($item) use ($parent) {
                    return array_merge($parent, [$item]);
                }, $array);
            }, $results));
        }, [[]]);
    }

    /**
     * Divide an array into two arrays. One with keys and the other with values.
     *
     * @param array $array
     *
     * @return array
     */
    public static function divide(array $array): array
    {
        return [array_keys($array), array_values($array)];
    }

    /**
     * Flatten a multi-dimensional associative array with dots.
     *
     * @param array $array
     * @param string $prepend
     *
     * @return array
     */
    public static function dot(array $array, string $prepend = ''): array
    {
        $results = [];

        foreach ($array as $key => $value) {
            if (is_array($value) && !empty($value)) {
                /** @noinspection SlowArrayOperationsInLoopInspection */
                $results = array_merge($results, static::dot($value, $prepend . $key . '.'));
            } else {
                $results[$prepend . $key] = $value;
            }
        }

        return $results;
    }

    /**
     * Get all of the given array except for a specified array of items.
     *
     * @param array $array
     * @param array|string $keys
     *
     * @return array
     */
    public static function except(array $array, array|string $keys): array
    {
        static::forget($array, $keys);

        return $array;
    }

    /**
     * Determine if the given key exists in the provided array.
     *
     * @param ArrayAccess|array $array
     * @param int|string $key
     *
     * @return bool
     */
    public static function exists(ArrayAccess|array $array, int|string $key): bool
    {
        if ($array instanceof ArrayAccess) {
            return $array->offsetExists($key);
        }

        return array_key_exists($key, $array);
    }

    /**
     * Flatten a multi-dimensional array into a single level.
     *
     * @param array $array
     * @param int $depth
     *
     * @return array
     */
    public static function flatten(array $array, int $depth = INF): array
    {
        return array_reduce($array, static function ($result, $item) use ($depth) {
            if (is_object($item) && method_exists($item, 'toArray')) {
                $item = $item->toArray();
            }

            if (!is_array($item)) {
                return array_merge($result, [$item]);
            }

            if ($depth === 1) {
                return array_merge($result, array_values($item));
            }

            return array_merge($result, static::flatten($item, $depth - 1));
        }, []);
    }

    /**
     * Remove one or many array items from a given array using "dot" notation.
     *
     * @param array $array
     * @param array|string $keys
     *
     * @return void
     */
    public static function forget(array &$array, array|string $keys): void
    {
        $original = &$array;
        $keys     = (array)$keys;

        if (count($keys) === 0) {
            return;
        }

        foreach ($keys as $key) {

            // if the exact key exists in the top-level, remove it
            if (static::exists($array, $key)) {
                unset($array[$key]);
                continue;
            }

            $parts = explode('.', $key);

            // clean up before each pass
            $array = &$original;

            while (count($parts) > 1) {
                $part = array_shift($parts);

                if (isset($array[$part]) && is_array($array[$part])) {
                    $array = &$array[$part];
                } else {
                    continue 2;
                }
            }

            unset($array[array_shift($parts)]);
        }
    }

    /**
     * Check if an item or items exist in an array using "dot" notation.
     *
     * @param Traversable|array $array
     * @param array|string $keys
     *
     * @return bool
     */
    public static function has(Traversable|array $array, array|string $keys): bool
    {
        if (!$array) {
            return false;
        }

        $keys = (array)$keys;
        if ($keys === []) {
            return false;
        }

        foreach ($keys as $key) {
            $subKeyArray = $array;
            if (static::exists($array, $key)) {
                continue;
            }

            foreach (explode('.', $key) as $segment) {
                if (static::accessible($subKeyArray) && static::exists($subKeyArray, $segment)) {
                    $subKeyArray = $subKeyArray[$segment];
                } else {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Push an item onto the beginning of an array.
     *
     * @param array $array
     * @param mixed $value
     * @param mixed|null $key
     *
     * @return array
     */
    public static function prepend(array $array, mixed $value, mixed $key = null): array
    {
        if (null === $key) {
            array_unshift($array, $value);
        } else {
            $array = [$key => $value] + $array;
        }

        return $array;
    }

    /**
     * remove the $key of the $arr, and return value.
     *
     * @param int|string $key
     * @param array $arr
     * @param mixed|null $default
     *
     * @return mixed
     */
    public static function remove(array &$arr, int|string $key, mixed $default = null): mixed
    {
        if (isset($arr[$key])) {
            $value = $arr[$key];
            unset($arr[$key]);
        } else {
            $value = $default;
        }

        return $value;
    }

    /**
     * @param array $arr
     * @param ...$keys
     *
     * @return array
     */
    public static function deleteKeys(array $arr, ...$keys): array
    {
        foreach ($keys as $key) {
            unset($arr[$key]);
        }
        return $arr;
    }

    /**
     * Get a value from the array, and remove it.
     *
     * @param ArrayAccess|array $array
     * @param int|string $key
     * @param mixed|null $default
     *
     * @return mixed
     */
    public static function pull(ArrayAccess|array $array, int|string $key, mixed $default = null): mixed
    {
        $value = static::get($array, $key, $default);

        static::forget($array, $key);

        return $value;
    }

    /**
     * Get a subset of the items from the given array.
     *
     * @param array $array
     * @param array|string $keys
     *
     * @return array
     */
    public static function only(array $array, array|string $keys): array
    {
        return array_intersect_key($array, array_flip((array)$keys));
    }

    /**
     * Shuffle the given array and return the result.
     *
     * @param array $array
     *
     * @return array
     */
    public static function shuffle(array $array): array
    {
        shuffle($array);

        return $array;
    }

    /**
     * Filter the array using the given callback.
     *
     * @param array $array
     * @param callable $callback
     *
     * @return array
     */
    public static function where(array $array, callable $callback): array
    {
        return array_filter($array, $callback, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * If the given value is not an array, wrap it in one.
     *
     * @param mixed $value
     *
     * @return array
     */
    public static function wrap(mixed $value): array
    {
        return !is_array($value) ? (array)$value : $value;
    }

    ////////////////////////////////////////////////////////////
    /// other
    ////////////////////////////////////////////////////////////

}
