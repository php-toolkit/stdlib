<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Str\Traits;

use function count;
use function function_exists;
use function html_entity_decode;
use function mb_detect_encoding;
use function mb_strlen;
use function mb_strwidth;
use function preg_match_all;
use function strlen;
use const ENT_COMPAT;

/**
 * Trait StringLengthHelperTrait
 *
 * @package Toolkit\Stdlib\Str\Traits
 */
trait StringLengthHelperTrait
{
    ////////////////////////////////////////////////////////////////////////
    /// Check Length
    ////////////////////////////////////////////////////////////////////////

    /**
     * from Symfony
     *
     * @param string|int $string
     *
     * @return int
     */
    public static function len($string): int
    {
        $string = (string)$string;
        if (false === $encoding = mb_detect_encoding($string, null, true)) {
            return strlen($string);
        }

        return mb_strwidth($string, $encoding);
    }

    /**
     * @param string $str
     * @param string $encoding
     *
     * @return int
     */
    public static function len2($str, string $encoding = 'UTF-8'): int
    {
        $str = (string)$str;

        return function_exists('mb_strlen') ? mb_strlen($str, $encoding) : strlen($str);
    }

    /**
     * @param string|mixed $str
     * @param string       $encoding
     * @param bool         $decodeHTML
     *
     * @return int
     */
    public static function strlen($str, string $encoding = 'UTF-8', bool $decodeHTML = false): int
    {
        if ($decodeHTML) {
            $str = html_entity_decode((string)$str, ENT_COMPAT, 'UTF-8');
        } else {
            $str = (string)$str;
        }

        return function_exists('mb_strlen') ? mb_strlen($str, $encoding) : strlen($str);
    }

    /**
     * @param string $string
     *
     * @return int
     */
    public static function utf8Len($string): int
    {
        // strlen: one chinese is 3 char.
        // mb_strlen: one chinese is 1 char.
        // mb_strwidth: one chinese is 2 char.
        return mb_strlen((string)$string, 'utf-8');
    }

    /**
     * @param string $string
     *
     * @return int
     */
    public static function utf8width($string): int
    {
        // strlen: one chinese is 3 char.
        // mb_strlen: one chinese is 1 char.
        // mb_strwidth: one chinese is 2 char.
        return mb_strwidth((string)$string, 'utf-8');
    }

    /**
     * 计算字符长度
     *
     * @param string $str
     *
     * @return int
     */
    public static function length(string $str): int
    {
        if ($str === '') {
            return 0;
        }

        if (function_exists('mb_strlen')) {
            return mb_strlen($str, 'utf-8');
        }

        preg_match_all('/./u', $str, $arr);

        return count($arr[0]);
    }

    /**
     * @from     web
     * 可以统计中文字符串长度的函数
     *
     * @param string $str 要计算长度的字符串
     *
     * @return int
     * @internal param bool $type 计算长度类型，0(默认)表示一个中文算一个字符，1表示一个中文算两个字符
     */
    public static function absLen(string $str): int
    {
        if (empty($str)) {
            return 0;
        }

        if (function_exists('mb_strwidth')) {
            return mb_strwidth($str, 'utf-8');
        }

        if (function_exists('mb_strlen')) {
            return mb_strlen($str, 'utf-8');
        }

        preg_match_all('/./u', $str, $ar);

        return count($ar[0]);
    }
}
