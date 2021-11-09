<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Str\Traits;

use Toolkit\Stdlib\Helper\DataHelper;
use Toolkit\Stdlib\Str\StringHelper;
use function array_filter;
use function array_map;
use function array_values;
use function count;
use function explode;
use function is_bool;
use function is_numeric;
use function mb_convert_encoding;
use function mb_convert_variables;
use function mb_detect_encoding;
use function mb_strwidth;
use function preg_quote;
use function preg_split;
use function str_contains;
use function str_pad;
use function str_split;
use function stripos;
use function strlen;
use function strpos;
use function trim;
use function vdump;
use const PREG_SPLIT_NO_EMPTY;

/**
 * Trait StringSplitHelperTrait
 *
 * @package Toolkit\Stdlib\Str\Traits
 */
trait StringConvertTrait
{
    /**
     * @param string $val
     *
     * @return bool|string
     */
    public static function tryToBool(string $val)
    {
        // check it is a bool value.
        if (false !== stripos(StringHelper::TRUE_WORDS, "|$val|")) {
            return true;
        }

        if (false !== stripos(StringHelper::FALSE_WORDS, "|$val|")) {
            return false;
        }

        return $val;
    }

    /**
     * @param string $str
     *
     * @return bool
     */
    public static function toBool(string $str): bool
    {
        return DataHelper::boolean($str);
    }

    /**
     * @param string $val
     *
     * @return bool
     */
    public static function toBool2(string $val): bool
    {
        // check it is a bool value.
        return false !== stripos(StringHelper::TRUE_WORDS, "|$val|");
    }

    /**
     * @param string $str
     *
     * @return bool
     */
    public static function toBoolTrue(string $str): bool
    {
        return DataHelper::boolean($str);
    }

    /**
     * auto convert string to typed value
     *
     * @param string $str
     * @param bool $parseBool
     * @param int $intMaxLen
     *
     * @return float|int|string|bool
     */
    public static function toTyped(string $str, bool $parseBool = false, int $intMaxLen = 11)
    {
        if (is_numeric($str) && strlen($str) <= $intMaxLen) {
            if (str_contains($str, '.')) {
                $val = (float)$str;
            } else {
                $val = (int)$str;
            }

            return $val;
        }

        // parse bool: true, false
        if ($parseBool && strlen($str) < 6) {
            return self::tryToBool($str);
        }

        return $str;
    }

    ////////////////////////////////////////////////////////////////////////
    /// split to array
    ////////////////////////////////////////////////////////////////////////

    /**
     * @param string $string
     * @param string $delimiter
     * @param int $limit
     *
     * @return array
     */
    public static function str2ints(string $string, string $delimiter = ',', int $limit = 0): array
    {
        return self::toInts($string, $delimiter, $limit);
    }

    /**
     * @param string $str
     * @param string $delimiter
     * @param int $limit
     *
     * @return array
     */
    public static function toInts(string $str, string $delimiter = ',', int $limit = 0): array
    {
        $intArr = [];
        // $values = self::splitTrimFiltered($str, $delimiter, $limit);
        $values = self::toNoEmptyArray($str, $delimiter, $limit);

        foreach ($values as $value) {
            if (is_numeric($value)) {
                $intArr[] = (int)$value;
            }
        }

        return $intArr;
    }

    /**
     * Like explode, but will trim each item and filter empty item.
     * - alias of toNoEmptyArray()
     *
     * @param string $str
     * @param string $separator
     * @param int $limit
     *
     * @return array
     */
    public static function explode(string $str, string $separator = '.', int $limit = 0): array
    {
        return self::toNoEmptyArray($str, $separator, $limit);
        // return self::splitTrimFiltered($str, $separator, $limit);
    }

    /**
     * alias of toNoEmptyArray()
     *
     * @param string $str
     * @param string $sep
     * @param int $limit
     *
     * @return array
     */
    public static function toArray(string $str, string $sep = ',', int $limit = 0): array
    {
        return self::toNoEmptyArray($str, $sep, $limit);;
    }

    /**
     * alias of toNoEmptyArray()
     *
     * @param string $str
     * @param string $sep
     * @param int $limit
     *
     * @return array
     */
    public static function str2array(string $str, string $sep = ',', int $limit = 0): array
    {
        return self::toNoEmptyArray($str, $sep, $limit);
    }

    /**
     * alias of toNoEmptyArray()
     *
     * @param string $str
     * @param string $delimiter
     * @param int $limit
     *
     * @return array
     */
    public static function split2Array(string $str, string $delimiter = ',', int $limit = 0): array
    {
        return self::toNoEmptyArray($str, $delimiter, $limit);
    }

    /**
     * like explode, split string to no empty array
     *
     * - Difference toNoEmptyArray() and splitTrimFiltered().
     *
     * Please see {@see StringHelperTest::testDiff_splitTrimFiltered_toNoEmptyArray()}
     * So, recommend use toNoEmptyArray() instead splitTrimFiltered()
     *
     * @param string $str
     * @param string $sep
     * @param int $limit
     *
     * @return array
     */
    public static function toNoEmptyArray(string $str, string $sep = ',', int $limit = -1): array
    {
        $str = trim($str, "$sep ");
        if (!$str) {
            return [];
        }

        if ($sep === ' ') {
            $pattern = '/\s+/';
        } else {
            $pattern = '/\s*' . preg_quote($sep, '/') . '\s*/';
        }

        return preg_split($pattern, $str, $limit, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * @param string $str
     * @param string $sep
     * @param int $limit
     *
     * @return array
     */
    public static function splitNoEmptyArray(string $str, string $sep = ',', int $limit = 0): array
    {
        return self::toNoEmptyArray($str, $sep, $limit);;
    }

    /**
     * Like explode, but will trim each item.
     *
     * @param string $str
     * @param string $delimiter
     * @param int $limit
     *
     * @return array
     */
    public static function toTrimmedArray(string $str, string $delimiter = ',', int $limit = 0): array
    {
        return self::splitTrimmed($str, $delimiter, $limit);
    }

    /**
     * Like explode, but will trim each item.
     *
     * @param string $str
     * @param string $delimiter
     * @param int $limit
     *
     * @return array
     */
    public static function splitTrimmed(string $str, string $delimiter = ',', int $limit = 0): array
    {
        $str = trim($str);
        if (!strpos($str, $delimiter)) {
            return [$str];
        }

        $list = $limit > 1 ? explode($delimiter, $str, $limit) : explode($delimiter, $str);

        return array_map('trim', $list);
    }

    /**
     * @param string $str
     * @param string $delimiter
     * @param int $intMaxLen
     *
     * @return array
     */
    public static function toTypedArray(string $str, string $delimiter = ',', int $intMaxLen = 11): array
    {
        return self::toTypedList($str, $delimiter, $intMaxLen);
    }

    /**
     * @param string $str
     * @param string $delimiter
     * @param int $intMaxLen
     *
     * @return array
     */
    public static function splitTypedList(string $str, string $delimiter = ',', int $intMaxLen = 11): array
    {
        return self::toTypedList($str, $delimiter, $intMaxLen);
    }

    /**
     * @param string $str
     * @param string $sep
     * @param int $intMaxLen
     *
     * @return array
     */
    public static function toTypedList(string $str, string $sep = ',', int $intMaxLen = 11): array
    {
        if (!$str) {
            return [];
        }

        // $arr = self::splitTrimFiltered($str, $delimiter);
        $arr = self::toNoEmptyArray($str, $sep);
        foreach ($arr as &$val) {
            $val = self::toTyped($val, true, $intMaxLen);
        }

        return $arr;
    }

    /**
     * Like explode, but will trim each item and filter empty item.
     *
     * @param string $str
     * @param string $delimiter
     * @param int $limit
     *
     * @return array
     */
    public static function splitTrimFiltered(string $str, string $delimiter = ',', int $limit = 0): array
    {
        if (!$str = trim($str)) {
            return [];
        }

        if (!strpos($str, $delimiter)) {
            return [$str];
        }

        $list = $limit < 1 ? explode($delimiter, $str) : explode($delimiter, $str, $limit);
        return array_values(array_filter(array_map('trim', $list), 'strlen'));
    }

    /**
     * @param string $string
     * @param int $width
     *
     * @return array
     */
    public static function splitByWidth(string $string, int $width = 1): array
    {
        // str_split is not suitable for multi-byte characters, we should use preg_split to get char array properly.
        // additionally, array_slice() is not enough as some character has doubled width.
        // we need a function to split string not by character count but by string width
        if (false === $encoding = mb_detect_encoding($string, null, true)) {
            return str_split($string, $width);
        }

        $utf8String = mb_convert_encoding($string, 'utf8', $encoding);
        $lines      = [];
        $line       = '';

        foreach (preg_split('//u', $utf8String) as $char) {
            // test if $char could be appended to current line
            if (mb_strwidth($line . $char, 'utf8') <= $width) {
                $line .= $char;
                continue;
            }

            // if not, push current line to array and make new line
            $lines[] = str_pad($line, $width);
            $line    = $char;
        }

        if ('' !== $line) {
            $lines[] = count($lines) ? str_pad($line, $width) : $line;
        }

        mb_convert_variables($encoding, 'utf8', $lines);
        return $lines;
    }

    /**
     * @param string $str
     *
     * @return array
     */
    public static function splitUtf8(string $str): array
    {
        return preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * @param string $str
     * @param int $length
     *
     * @return string[]
     * @link https://www.php.net/manual/zh/function.str-split.php
     */
    public static function splitUnicode(string $str, int $length = 1): array
    {
        if ($length > 1) {
            $ret = [];
            $len = mb_strlen($str, 'UTF-8');
            for ($i = 0; $i < $len; $i += $length) {
                $ret[] = mb_substr($str, $i, $length, 'UTF-8');
            }

            return $ret;
        }

        return preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * @param string $str
     * @param int $length
     *
     * @return string[]
     * @link https://www.php.net/manual/zh/function.str-split.php
     */
    public static function splitUnicode2(string $str, int $length = 1): array
    {
        $tmp = preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY);

        if ($length > 1) {
            $chunks = array_chunk($tmp, $length);
            foreach ($chunks as $i => $chunk) {
                $chunks[$i] = implode('', (array)$chunk);
            }
            $tmp = $chunks;
        }

        return $tmp;
    }
}
