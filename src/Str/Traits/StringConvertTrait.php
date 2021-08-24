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
use function array_map;
use function array_values;
use function count;
use function explode;
use function mb_convert_encoding;
use function mb_convert_variables;
use function mb_detect_encoding;
use function mb_strwidth;
use function preg_split;
use function str_pad;
use function str_split;
use function strpos;
use function trim;
use const PREG_SPLIT_NO_EMPTY;

/**
 * Trait StringSplitHelperTrait
 *
 * @package Toolkit\Stdlib\Str\Traits
 */
trait StringConvertTrait
{
    /**
     * @param string $str
     *
     * @return bool
     */
    public static function toBool(string $str): bool
    {
        return DataHelper::boolean($str);
    }

    ////////////////////////////////////////////////////////////////////////
    /// split to array
    ////////////////////////////////////////////////////////////////////////

    /**
     * var_dump(str2array('34,56,678, 678, 89, '));
     *
     * @param string $str
     * @param string $sep
     *
     * @return array
     */
    public static function str2array(string $str, string $sep = ','): array
    {
        $str = trim($str, "$sep ");

        if (!$str) {
            return [];
        }

        return preg_split("/\s*$sep\s*/", $str, -1, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * @param string $string
     * @param string $delimiter
     * @param int    $limit
     *
     * @return array
     */
    public static function str2ints(string $string, string $delimiter = ',', int $limit = 0): array
    {
        $values = self::toArray($string, $delimiter, $limit);

        return array_map('intval', $values);
    }

    /**
     * @param string $string
     * @param string $delimiter
     * @param int    $limit
     *
     * @return array
     */
    public static function toInts(string $string, string $delimiter = ',', int $limit = 0): array
    {
        return self::str2ints($string, $delimiter, $limit);
    }

    /**
     * @param string $string
     * @param string $delimiter
     * @param int    $limit
     *
     * @return array
     */
    public static function toArray(string $string, string $delimiter = ',', int $limit = 0): array
    {
        $string = trim($string, "$delimiter ");
        if ($string === '') {
            return [];
        }

        $values  = [];
        $rawList = $limit < 1 ? explode($delimiter, $string) : explode($delimiter, $string, $limit);

        foreach ($rawList as $val) {
            if (($val = trim($val)) !== '') {
                $values[] = $val;
            }
        }

        return $values;
    }

    /**
     * @param string $str
     * @param string $separator
     * @param int    $limit
     *
     * @return array
     */
    public static function explode(string $str, string $separator = '.', int $limit = 0): array
    {
        return static::split2Array($str, $separator, $limit);
    }

    /**
     * @param string $str
     * @param string $delimiter
     * @param int    $limit
     *
     * @return array
     */
    public static function split2Array(string $str, string $delimiter = ',', int $limit = 0): array
    {
        $str = trim($str, "$delimiter ");

        if (!strpos($str, $delimiter)) {
            return [$str];
        }

        if ($limit < 1) {
            $list = explode($delimiter, $str);
        } else {
            $list = explode($delimiter, $str, $limit);
        }

        return array_values(array_filter(array_map('trim', $list), 'strlen'));
    }

    /**
     * @param string $str
     * @param string $delimiter
     * @param int    $limit
     *
     * @return array
     */
    public static function splitTrimmed(string $str, string $delimiter = ',', int $limit = 0): array
    {
        $str = trim($str, "$delimiter ");

        if (!strpos($str, $delimiter)) {
            return [$str];
        }

        $list = $limit > 1 ? explode($delimiter, $str, $limit) : explode($delimiter, $str);

        return array_map('trim', $list);
    }

    /**
     * @param string $string
     * @param int    $width
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
     * @param int    $length
     *
     * @return array|string[]
     * @link https://www.php.net/manual/zh/function.str-split.php
     */
    public static function splitUnicode(string $str, int $length = 1): array
    {
        if ($length > 0) {
            $ret = [];
            $len = mb_strlen($str, 'UTF-8');
            for ($i = 0; $i < $len; $i += $length) {
                $ret[] = mb_substr($str, $i, $length, 'UTF-8');
            }

            return $ret;
        }

        return preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY);
    }
}
