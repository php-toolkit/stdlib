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
use ArrayObject;
use stdClass;
use Toolkit\Stdlib\Helper\DataHelper;
use Traversable;
use function array_change_key_case;
use function array_diff;
use function array_filter;
use function array_intersect;
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
use function get_class;
use function gettype;
use function implode;
use function in_array;
use function is_array;
use function is_numeric;
use function is_object;
use function is_resource;
use function is_scalar;
use function is_string;
use function mb_strlen;
use function method_exists;
use function strlen;
use function strtolower;
use function trim;

/**
 * Class ArrayHelper
 *
 * @package Toolkit\Stdlib\Arr
 */
class ArrayHelper
{
    use Traits\ArrayMergeTrait;
    use Traits\ArrayValueGetSetTrait;

    /**
     * Determine whether the given value is array accessible.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public static function accessible($value): bool
    {
        return is_array($value) || $value instanceof ArrayAccess;
    }

    /**
     * Determines if an array is associative.
     * An array is "associative" if it doesn't have sequential numerical keys beginning with zero.
     *
     * @param array $array
     *
     * @return bool
     */
    public static function isAssoc(array $array): bool
    {
        $keys = array_keys($array);

        return array_keys($keys) !== $keys;
    }

    /**
     * @param mixed $array
     *
     * @return Traversable
     */
    public static function toIterator($array): Traversable
    {
        if (!$array instanceof Traversable) {
            $array = new ArrayObject((array)$array);
        }

        return $array;
    }

    /**
     * array data to object
     *
     * @param array|Traversable $array
     * @param string            $class
     *
     * @return mixed
     */
    public static function toObject($array, string $class = stdClass::class)
    {
        $object = new $class;

        foreach ($array as $name => $value) {
            $name = trim($name);

            if (!$name || is_numeric($name)) {
                continue;
            }

            $object->$name = is_array($value) ? self::toObject($value) : $value;
        }

        return $object;
    }

    /**
     * 清理数组值的空白
     *
     * @param array $data
     *
     * @return array|string
     */
    public static function valueTrim(array $data)
    {
        if (is_scalar($data)) {
            return trim($data);
        }

        array_walk_recursive($data, static function (&$value): void {
            $value = trim($value);
        });

        return $data;
    }

    /**
     * 不区分大小写检测数据键名是否存在
     *
     * @param int|string $key
     * @param array      $arr
     *
     * @return bool
     */
    public static function keyExists($key, array $arr): bool
    {
        return array_key_exists(strtolower($key), array_change_key_case($arr));
    }

    /**
     * @param array $arr
     *
     * @return array
     */
    public static function valueToLower(array $arr): array
    {
        return self::changeValueCase($arr, false);
    }

    /**
     * @param array $arr
     *
     * @return array
     */
    public static function valueToUpper(array $arr): array
    {
        return self::changeValueCase($arr);
    }

    /**
     * 将数组中的值全部转为大写或小写
     *
     * @param array|iterable $arr
     * @param bool           $toUpper
     *
     * @return array
     */
    public static function changeValueCase($arr, bool $toUpper = true): array
    {
        $function = $toUpper ? 'strtoupper' : 'strtolower';
        $newArr   = []; //格式化后的数组

        foreach ($arr as $k => $v) {
            if (is_array($v)) {
                $newArr[$k] = self::changeValueCase($v, $toUpper);
            } else {
                $v          = trim($v);
                $newArr[$k] = $function($v);
            }
        }

        return $newArr;
    }

    /**
     * ******* 检查 一个或多个值是否全部存在数组中 *******
     * 有一个不存在即返回 false
     *
     * @param string|array $check
     * @param array        $sampleArr 只能检查一维数组
     *                                注： 不分类型， 区分大小写  2 == '2' ‘a' != 'A'
     *
     * @return bool
     */
    public static function valueExistsAll($check, array $sampleArr): bool
    {
        // 以逗号分隔的会被拆开，组成数组
        if (is_string($check)) {
            $check = trim($check, ', ');
            $check = str_contains($check, ',') ? explode(',', $check) : [$check];
        }

        return !array_diff((array)$check, $sampleArr);
    }

    /**
     * ******* 检查 一个或多个值是否存在数组中 *******
     * 有一个存在就返回 true 都不存在 return false
     *
     * @param string|array $check
     * @param array        $sampleArr 只能检查一维数组
     *
     * @return bool
     */
    public static function valueExistsOne($check, array $sampleArr): bool
    {
        // 以逗号分隔的会被拆开，组成数组
        if (is_string($check)) {
            $check = trim($check, ', ');
            $check = str_contains($check, ',') ? explode(',', $check) : [$check];
        }

        return (bool)array_intersect((array)$check, $sampleArr);
    }

    /**
     * ******* 不区分大小写，检查 一个或多个值是否 全存在数组中 *******
     * 有一个不存在即返回 false
     *
     * @param string|array   $need
     * @param array|iterable $arr  只能检查一维数组
     * @param bool           $type 是否同时验证类型
     *
     * @return bool | string 不存在的会返回 检查到的 字段，判断时 请使用 ArrHelper::existsAll($need,$arr)===true 来验证是否全存在
     */
    public static function existsAll($need, $arr, bool $type = false)
    {
        if (is_array($need)) {
            foreach ((array)$need as $v) {
                self::existsAll($v, $arr, $type);
            }
        } elseif (str_contains($need, ',')) {
            $need = explode(',', $need);
            self::existsAll($need, $arr, $type);
        } else {
            $arr  = self::valueToLower($arr);//小写
            $need = strtolower(trim($need));//小写

            if (!in_array($need, $arr, $type)) {
                return $need;
            }
        }

        return true;
    }

    /**
     * ******* 不区分大小写，检查 一个或多个值是否存在数组中 *******
     * 有一个存在就返回 true 都不存在 return false
     *
     * @param string|array   $need
     * @param array|iterable $arr  只能检查一维数组
     * @param bool           $type 是否同时验证类型
     *
     * @return bool
     */
    public static function existsOne($need, $arr, bool $type = false): bool
    {
        if (is_array($need)) {
            foreach ((array)$need as $v) {
                $result = self::existsOne($v, $arr, $type);
                if ($result) {
                    return true;
                }
            }
        } else {
            if (str_contains($need, ',')) {
                $need = explode(',', $need);

                return self::existsOne($need, $arr, $type);
            }

            $arr  = self::changeValueCase($arr);//小写
            $need = strtolower($need);//小写

            if (in_array($need, $arr, $type)) {
                return true;
            }
        }

        return false;
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
     * @param bool  $excludeInt
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
     * @param bool  $excludeInt
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
     * @param array  $array
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
     * @param array        $array
     * @param array|string $keys
     *
     * @return array
     */
    public static function except(array $array, $keys): array
    {
        static::forget($array, $keys);

        return $array;
    }

    /**
     * Determine if the given key exists in the provided array.
     *
     * @param ArrayAccess|array $array
     * @param string|int        $key
     *
     * @return bool
     */
    public static function exists(array $array, $key): bool
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
     * @param int   $depth
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
     * @param array        $array
     * @param array|string $keys
     *
     * @return void
     */
    public static function forget(array &$array, $keys): void
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
     * @param ArrayAccess|array $array
     * @param string|array      $keys
     *
     * @return bool
     */
    public static function has($array, $keys): bool
    {
        if (null === $keys) {
            return false;
        }

        $keys = (array)$keys;

        if (!$array) {
            return false;
        }

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
     * @param mixed $key
     *
     * @return array
     */
    public static function prepend(array $array, $value, $key = null): array
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
     * @param string|int $key
     * @param array      $arr
     * @param mixed      $default
     *
     * @return mixed
     */
    public static function remove(array &$arr, $key, $default = null)
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
     * Get a value from the array, and remove it.
     *
     * @param array|ArrayAccess $array
     * @param string|int        $key
     * @param mixed             $default
     *
     * @return mixed
     */
    public static function pull(&$array, $key, $default = null)
    {
        $value = static::get($array, $key, $default);

        static::forget($array, $key);

        return $value;
    }

    /**
     * Get a subset of the items from the given array.
     *
     * @param array        $array
     * @param array|string $keys
     *
     * @return array
     */
    public static function only(array $array, $keys): array
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
     * @param array    $array
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
    public static function wrap($value): array
    {
        return !is_array($value) ? (array)$value : $value;
    }

    ////////////////////////////////////////////////////////////
    /// other
    ////////////////////////////////////////////////////////////

    /**
     * array 递归 转换成 字符串
     *
     * @param array  $array
     * @param int    $length
     * @param int    $cycles 至多循环六次 $num >= 6
     * @param bool   $showKey
     * @param bool   $addMark
     * @param string $separator
     * @param string $string
     *
     * @return string
     */
    public static function toString(
        $array,
        int $length = 800,
        int $cycles = 6,
        bool $showKey = true,
        bool $addMark = false,
        string $separator = ', ',
        string $string = ''
    ): string {
        if (!is_array($array) || empty($array)) {
            return '';
        }

        $mark = $addMark ? '\'' : '';
        $num  = 0;

        foreach ($array as $key => $value) {
            $num++;

            if ($num >= $cycles || strlen($string) > (int)$length) {
                $string .= '... ...';
                break;
            }

            $keyStr = $showKey ? $key . '=>' : '';

            if (is_array($value)) {
                $string .= $keyStr . 'Array(' . self::toString(
                        $value,
                        $length,
                        $cycles,
                        $showKey,
                        $addMark,
                        $separator,
                        $string
                    ) . ')' . $separator;
            } elseif (is_object($value)) {
                $string .= $keyStr . 'Object(' . get_class($value) . ')' . $separator;
            } elseif (is_resource($value)) {
                $string .= $keyStr . 'Resource(' . get_resource_type($value) . ')' . $separator;
            } else {
                $value  = strlen($value) > 150 ? substr($value, 0, 150) : $value;
                $string .= $mark . $keyStr . trim(htmlspecialchars($value)) . $mark . $separator;
            }
        }

        return trim($string, $separator);
    }

    public static function toStringNoKey(
        $array,
        $length = 800,
        $cycles = 6,
        $showKey = false,
        $addMark = true,
        $separator = ', '
    ): string {
        return static::toString($array, $length, $cycles, $showKey, $addMark, $separator);
    }

    /**
     * @param array $array
     * @param int   $length
     *
     * @return string
     */
    public static function toFormatString(array $array, int $length = 400): string
    {
        $string = var_export($array, true);

        # 将非空格替换为一个空格
        $string = preg_replace('/[\n\r\t]/', ' ', $string);
        # 去掉两个空格以上的
        $string = preg_replace('/\s(?=\s)/', '', $string);
        $string = trim($string);

        if (strlen($string) > $length) {
            $string = substr($string, 0, $length) . '...';
        }

        return $string;
    }

    /**
     * @param $array
     *
     * @return array
     */
    public static function toLimitOut($array): array
    {
        if (!is_array($array)) {
            return $array;
        }

        // static $num = 1;

        foreach ($array as $key => $value) {
            // if ( $num >= $cycles) {
            //     break;
            // }

            if (is_array($value) || is_object($value)) {
                $value = gettype($value) . '(...)';
            } elseif (is_string($value) || is_numeric($value)) {
                $value = strlen(trim($value));
            } else {
                $value = gettype($value) . "($value)";
            }

            $array[$key] = $value;
        }

        // $num++;
        return $array;
    }

    /**
     * @param array $data
     * @param string $kvSep
     * @param string $lineSep
     *
     * @return string
     */
    public static function toKVString(array $data, string $kvSep = '=', string $lineSep = "\n"): string
    {
        $lines = [];
        foreach ($data as $key => $val) {
            $lines = $key . $kvSep . DataHelper::toString($val);
        }

        return implode($lineSep, $lines);
    }
}
