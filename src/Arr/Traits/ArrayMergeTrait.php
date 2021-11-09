<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Arr\Traits;

use RuntimeException;
use function array_merge;
use function array_shift;
use function is_array;
use function is_int;

/**
 * Trait ArrayMergeTrait
 *
 * @package Toolkit\Stdlib\Arr\Traits
 */
trait ArrayMergeTrait
{
    /**
     * 替换值合并数组
     * - 只会将同时存在于两个数组的key，使用第二个数组对应的值替换
     *
     * @param array $base
     * @param array $replacements
     *
     * @return array
     */
    public static function replace(array $base, array $replacements): array
    {
        foreach ($base as $key => $value) {
            if (isset($replacements[$key])) {
                $base[$key] = $replacements[$key];
            }
        }

        return $base;
    }

    /**
     * quick merge `append` array to `base` array.
     * - Process at most secondary arrays
     *
     * @param array $append
     * @param array $base
     *
     * @return array
     */
    public static function quickMerge(array $append, array $base): array
    {
        foreach ($append as $key => $item) {
            // value is array. merge sub-array.
            if (isset($base[$key]) && is_array($base[$key])) {
                if (is_array($item)) {
                    $base[$key] = array_merge($base[$key], $item);
                } else {
                    throw new RuntimeException("Array merge error! the '$key' must be an array");
                }
            } else { // set new value OR add new key.
                $base[$key] = $item;
            }
        }

        return $base;
    }

    /**
     * Recursion merge new array to src array.
     * 递归合并两个多维数组,后面的值将会递归覆盖原来的值
     *
     * @param array $src
     * @param array $new
     * @param int $depth max merge depth
     *
     * @return array
     */
    public static function merge(array $src, array $new, int $depth = 10): array
    {
        if (!$src) {
            return $new;
        }

        if (!$new) {
            return $src;
        }

        foreach ($new as $key => $value) {
            if (is_int($key)) {
                if (isset($src[$key])) {
                    $src[] = $value;
                } else {
                    $src[$key] = $value;
                }
            } elseif ($depth > 0 && isset($src[$key]) && is_array($value)) {
                $src[$key] = self::merge($src[$key], $value, --$depth);
            } else {
                $src[$key] = $value;
            }
        }

        return $src;
    }

    /**
     * 递归合并多个多维数组,
     *
     * @from yii2
     * Merges two or more arrays into one recursively.
     *
     * @param array $args
     *
     * @return array the merged array (the original arrays are not changed.)
     */
    public static function merge2(...$args): array
    {
        /** @var array[] $args */
        $res = array_shift($args);

        while (!empty($args)) {
            /** @var array $next */
            $next = array_shift($args);

            foreach ($next as $k => $v) {
                if (is_int($k)) {
                    if (isset($res[$k])) {
                        $res[] = $v;
                    } else {
                        $res[$k] = $v;
                    }
                } elseif (is_array($v) && isset($res[$k]) && is_array($res[$k])) {
                    $res[$k] = self::merge2($res[$k], $v);
                } else {
                    $res[$k] = $v;
                }
            }
        }

        return $res;
    }
}
