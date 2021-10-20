<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Str\Traits;

use JetBrains\PhpStorm\Pure;
use function function_exists;
use function is_array;
use function is_string;
use function mb_strpos;
use function mb_strrpos;
use function preg_match;
use function str_ends_with;
use function str_starts_with;
use function stripos;
use function strpos;
use function strrpos;
use function strtolower;
use function trim;

/**
 * Trait StringCheckHelperTrait
 *
 * @package Toolkit\Stdlib\Str\Traits
 */
trait StringCheckHelperTrait
{
    ////////////////////////////////////////////////////////////////////////
    /// Check value
    ////////////////////////////////////////////////////////////////////////

    /**
     * @param string $string
     * @param string $prefix
     * @param string $suffix
     * @return string
     */
    public static function optional(string $string, string $prefix = ' ', string $suffix = ''): string
    {
        if (empty($string)) {
            return '';
        }

        return $prefix . $string . $suffix;
    }

    /**
     * @param string       $string
     * @param string|array $needle
     * @return bool
     */
    public static function notContains(string $string, $needle): bool
    {
        return !self::has($string, $needle);
    }

    /**
     * @param string       $string
     * @param string|array $needle
     * @return bool
     */
    public static function contains(string $string, $needle): bool
    {
        return self::has($string, $needle);
    }

    /**
     * @param string       $string
     * @param string|array $needle
     * @return bool
     */
    public static function has(string $string, $needle): bool
    {
        if (is_string($needle)) {
            return str_contains($string, $needle);
        }

        if (is_array($needle)) {
            foreach ((array)$needle as $item) {
                if (str_contains($string, $item)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Alias of the `ihas()`
     *
     * @param string       $string
     * @param string|array $needle
     * @return bool
     */
    public static function icontains(string $string, $needle): bool
    {
        return self::ihas($string, $needle);
    }

    /**
     * Like `has` but will ignore case
     *
     * @param string       $string
     * @param string|array $needle
     * @return bool
     */
    public static function ihas(string $string, $needle): bool
    {
        if (is_string($needle)) {
            return stripos($string, $needle) !== false;
        }

        if (is_array($needle)) {
            foreach ((array)$needle as $item) {
                if (stripos($string, $item) !== false) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Alias of the `self::strpos()`
     *
     * @param string $str
     * @param string $find
     * @param int    $offset
     * @param string $encoding
     *
     * @return false|int
     */
    public static function pos(string $str, string $find, int $offset = 0, string $encoding = 'UTF-8')
    {
        return self::strpos($str, $find, $offset, $encoding);
    }

    /**
     * @param string $str
     * @param string $find
     * @param int    $offset
     * @param string $encoding
     *
     * @return false|int
     */
    public static function strpos(string $str, string $find, int $offset = 0, string $encoding = 'UTF-8')
    {
        return function_exists('mb_strpos') ?
            mb_strpos($str, $find, $offset, $encoding) :
            strpos($str, $find, $offset);
    }

    /**
     * Alias of the `self::stripos()`
     *
     * @param string $str
     * @param string $find
     * @param int    $offset
     * @param string $encoding
     *
     * @return false|int
     */
    public static function ipos(string $str, string $find, int $offset = 0, string $encoding = 'UTF-8')
    {
        return self::strpos($str, $find, $offset, $encoding);
    }

    /**
     * @param string $str
     * @param string $find
     * @param int    $offset
     * @param string $encoding
     *
     * @return false|int
     */
    public static function stripos(string $str, string $find, int $offset = 0, string $encoding = 'UTF-8')
    {
        return function_exists('mb_stripos') ?
            mb_stripos($str, $find, $offset, $encoding) :
            stripos($str, $find, $offset);
    }

    /**
     * @param string $str
     * @param string $find
     * @param int    $offset
     * @param string $encoding
     * @return bool|int
     */
    public static function strrpos(string $str, string $find, int $offset = 0, string $encoding = 'utf-8')
    {
        return function_exists('mb_strrpos') ?
            mb_strrpos($str, $find, $offset, $encoding) :
            strrpos($str, $find, $offset);
    }

    /**
     * @param string $str
     * @param string $needle
     *
     * @return bool
     */
    public static function isStartWiths(string $str, string $needle): bool
    {
        return self::hasPrefix($str, $needle);
    }

    /**
     * @param string $str
     * @param string $needle
     *
     * @return bool
     */
    public static function hasPrefix(string $str, string $needle): bool
    {
        return str_starts_with($str, $needle);
    }

    /**
     * @param string $str
     * @param string $needle
     *
     * @return bool
     */
    public static function isEndWiths(string $str, string $needle): bool
    {
        return self::hasSuffix($str, $needle);
    }

    /**
     * @param string $str
     * @param string $needle
     *
     * @return bool
     */
    public static function hasSuffix(string $str, string $needle): bool
    {
        return str_ends_with($str, $needle);
    }

    /**
     * 检查字符串是否是正确的变量名
     *
     * @param string $string
     * @return bool
     */
    public static function isVarName(string $string): bool
    {
        return preg_match('@^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*@i', $string) === 1;
    }

    /**
     * 使用正则验证数据
     *
     * @param string $value 要验证的数据
     * @param string $rule 验证规则 require email url currency number integer english
     * @return boolean
     */
    public static function match(string $value, string $rule): bool
    {
        $validate = [
            'require'  => '/\S+/',
            'email'    => '/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/',
            // 'url'       =>  '/^http(s?):\/\/(?:[A-za-z0-9-]+\.)+[A-za-z]{2,4}(?:[\/\?#][\/=\?%\-&~`@[\]\':+!\.#\w]*)?$/',
            'url'      => '/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i',
            'currency' => '/^\d+(\.\d+)?$/',
            # 货币
            'number'   => '/^\d+$/',
            'zip'      => '/^\d{6}$/',
            'integer'  => '/^[-\+]?\d+$/',
            'double'   => '/^[-\+]?\d+(\.\d+)?$/',
            'english'  => '/^[A-Za-z]+$/',
        ];

        $value = trim($value);
        $name  = strtolower($rule);

        // 检查是否有内置的正则表达式
        if (isset($validate[$name])) {
            $rule = $validate[$name];
        }

        return preg_match($rule, $value) === 1;
    }
}
