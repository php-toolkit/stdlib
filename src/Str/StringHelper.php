<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Str;

use DateTime;
use Exception;
use Toolkit\Stdlib\Str\Traits\StringCaseHelperTrait;
use Toolkit\Stdlib\Str\Traits\StringCheckHelperTrait;
use Toolkit\Stdlib\Str\Traits\StringLengthHelperTrait;
use Toolkit\Stdlib\Str\Traits\StringConvertTrait;
use Toolkit\Stdlib\Str\Traits\StringOtherHelperTrait;
use Toolkit\Stdlib\Str\Traits\StringTruncateHelperTrait;
use Toolkit\Stdlib\Util\UUID;
use function abs;
use function array_merge;
use function base64_encode;
use function count;
use function crc32;
use function escapeshellarg;
use function explode;
use function gethostname;
use function hash;
use function hex2bin;
use function is_int;
use function is_string;
use function mb_strwidth;
use function microtime;
use function preg_match;
use function preg_split;
use function random_bytes;
use function random_int;
use function sprintf;
use function str_contains;
use function str_pad;
use function str_repeat;
use function str_replace;
use function str_word_count;
use function strlen;
use function strtr;
use function substr;
use function trim;
use function uniqid;
use const STR_PAD_LEFT;
use const STR_PAD_RIGHT;

/**
 * Class StringHelper
 *
 * @package Toolkit\Stdlib\Str
 */
abstract class StringHelper
{
    // These words will be as a Boolean value
    public const TRUE_WORDS  = '|on|yes|true|';
    public const FALSE_WORDS = '|off|no|false|';

    public static $defaultEncoding = 'UTF-8';

    use StringCaseHelperTrait;
    use StringCheckHelperTrait;
    use StringLengthHelperTrait;
    use StringConvertTrait;
    use StringTruncateHelperTrait;
    use StringOtherHelperTrait;

    /**
     * @param string|mixed $str
     * @param int|float    $padLen
     * @param string       $padStr
     * @param int          $padType
     *
     * @return string
     */
    public static function pad($str, $padLen, string $padStr = ' ', int $padType = STR_PAD_RIGHT): string
    {
        return $padLen > 0 ? str_pad((string)$str, (int)$padLen, $padStr, $padType) : (string)$str;
    }

    /**
     * @param string|mixed $str
     * @param int|float    $padLen
     * @param string       $padStr
     *
     * @return string
     */
    public static function padLeft($str, $padLen, string $padStr = ' '): string
    {
        return $padLen > 0 ? str_pad((string)$str, (int)$padLen, $padStr, STR_PAD_LEFT) : (string)$str;
    }

    /**
     * @param string|mixed $str
     * @param int|float    $padLen
     * @param string       $padStr
     *
     * @return string
     */
    public static function padRight($str, $padLen, string $padStr = ' '): string
    {
        return $padLen > 0 ? str_pad((string)$str, (int)$padLen, $padStr) : (string)$str;
    }

    /**
     * @param string|mixed $str
     * @param int|float    $padLen
     * @param string $padStr
     * @param int    $padType
     *
     * @return string
     */
    public static function padByWidth($str, $padLen, string $padStr = ' ', int $padType = STR_PAD_RIGHT): string
    {
        $stringWidth = mb_strwidth((string)$str, self::$defaultEncoding);
        if ($stringWidth >= $padLen) {
            return (string)$str;
        }

        $repeatTimes = (int)$padLen - $stringWidth;
        $buildString = str_repeat($padStr, $repeatTimes);

        return $padType === STR_PAD_RIGHT ? $str . $buildString : $buildString . $str;
    }

    /**
     * @param string|int $str
     * @param string|int $times
     *
     * @return string
     */
    public static function repeat($str, $times): string
    {
        return str_repeat((string)$str, (int)$times);
    }

    ////////////////////////////////////////////////////////////
    /// Security
    ////////////////////////////////////////////////////////////

    /**
     * ********************** 生成一定长度的随机字符串函数 **********************
     *
     * @param int          $length - 随机字符串长度
     * @param array|string $param  -
     *
     * @return string
     * @throws Exception
     * @internal param string $chars
     */
    public static function random(int $length, array $param = []): string
    {
        $param = array_merge([
            'prefix' => '',
            'suffix' => '',
            'chars'  => 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'
        ], $param);

        $chars = $param['chars'];
        $max   = strlen($chars) - 1;   //strlen($chars) 计算字符串的长度
        $str   = '';

        for ($i = 0; $i < $length; $i++) {
            $str .= $chars[random_int(0, $max)];
        }

        return $param['prefix'] . $str . $param['suffix'];
    }

    /**
     * @param int $length
     *
     * @return string
     * @throws Exception
     */
    public static function genSalt(int $length = 32): string
    {
        return substr(
            str_replace('+', '.', base64_encode(hex2bin(random_bytes($length)))),
            0,
            44
        );
    }

    /**
     * @param int $length
     *
     * @return bool|string
     */
    public static function genUid(int $length = 7): string
    {
        if (!is_int($length) || $length > 32 || $length < 1) {
            $length = 7;
        }

        return substr(hash('md5', uniqid('', true)), 0, $length);
    }

    /**
     * @param string $prefix
     *
     * @return string
     */
    public static function uniqId(string $prefix = ''): string
    {
        return uniqid($prefix, true);
    }

    /**
     * gen UUID
     *
     * @param int  $version
     * @param null $node
     * @param null $ns
     *
     * @return UUID
     * @throws Exception
     */
    public static function genUUID(int $version = 1, $node = null, $ns = null): UUID
    {
        return UUID::generate($version, $node, $ns);
    }

    /**
     * Generate order number
     *
     * @param string|int $prefix
     *
     * @return string If no prefix, default length is 20
     * @throws Exception
     */
    public static function genNOV1($prefix = '', array $randomRange = []): string
    {
        $host = gethostname();
        $time = microtime(true) * 10000;

        $id = (string)abs(crc32($host) % 100);
        $id = str_pad($id, 2, '0', STR_PAD_LEFT);

        $randomRange = $randomRange ?: [1000, 9999];
        [$min, $max] = $randomRange;
        /** @noinspection PhpUnhandledExceptionInspection */
        $random = random_int($min, $max);

        return $prefix . $time . $id . $random;
    }

    /**
     * Generate order number v2
     *
     * @param string|int $prefix
     *
     * @return string If no prefix, default length is 26
     * @throws Exception
     */
    public static function genNOV2($prefix = '', array $randomRange = []): string
    {
        $host = gethostname();
        // u - 可以打印微妙，但是使用 date 函数时无效
        // $date = date('YmdHisu');
        $date = (new DateTime())->format('YmdHisu');

        $id = (string)abs(crc32($host) % 100);
        $id = str_pad($id, 2, '0', STR_PAD_LEFT);

        $randomRange = $randomRange ?: [100, 999];
        [$min, $max] = $randomRange;
        /** @noinspection PhpUnhandledExceptionInspection */
        $random = random_int($min, $max);

        return $prefix . $date . $id . $random;
    }

    /**
     * @param string $tplCode
     * @param array  $vars
     *
     * @return string
     */
    public static function replaces(string $tplCode, array $vars): string
    {
        return strtr($tplCode, $vars);
    }

    /**
     * @param string $tplCode
     * @param array $vars
     * @param string $format
     *
     * @return string
     */
    public static function renderTemplate(string $tplCode, array $vars, string $format = '{{%s}}'): string
    {
        // get left chars
        [$left, ] = explode('%s', $format);
        if (!$vars || !str_contains($tplCode, $left)) {
            return $tplCode;
        }

        $fmtVars = [];
        foreach ($vars as $name => $var) {
            $name = sprintf($format, (string)$name);
            // add
            $fmtVars[$name] = $var;
        }

        return strtr($tplCode, $fmtVars);
    }

    ////////////////////////////////////////////////////////////
    /// Other
    ////////////////////////////////////////////////////////////

    /**
     * @param string $str
     *
     * @return int
     */
    public static function wordCount(string $str): int
    {
        return str_word_count($str);
    }

    /**
     * @param string $str
     *
     * @return int
     */
    public static function utf8WordCount(string $str): int
    {
        return count(preg_split('~[^\p{L}\p{N}\']+~u', $str));
    }

    /**
     * Escapes a token through escape shell arg if it contains unsafe chars.
     *
     * @param string $token
     *
     * @return string
     */
    public static function escapeToken(string $token): string
    {
        return preg_match('{^[\w-]+$}', $token) ? $token : escapeshellarg($token);
    }

    /**
     * @param string $str
     *
     * @return string
     */
    public static function removeQuotes(string $str): string
    {
        if (preg_match("/^\".*\"$/", $str) || preg_match("/^'.*'$/", $str)) {
            return mb_substr($str, 1, -1);
        }

        return $str;
    }

    /**
     * @param string $str
     * @param bool $quoteAll
     *
     * @return string
     */
    public static function paramQuotes(string $str, bool $quoteAll = false): string
    {
        if ($str === '') {
            return "''";
        }

        if (
            !$quoteAll &&
            (preg_match("/^\".*\"$/", $str) || preg_match("/^'.*'$/", $str))
        ) {
            return $str;
        }

        $quote = str_contains($str, "'") ? '"' : "'";

        return $quote . $str . $quote;
    }

    /**
     * @param array $list
     * @param string $wrapChar
     *
     * @return array
     */
    public static function wrapList(array $list, string $wrapChar): array
    {
        $new = [];
        foreach ($list as $val) {
            $new[] = self::wrap($val, $wrapChar);
        }

        return $new;
    }

    /**
     * @param string|int|mixed $str
     * @param string $wrapChar
     *
     * @return string
     */
    public static function wrap($str, string $wrapChar): string
    {
        $str = (string)$str;
        if ($str === '') {
            return $str;
        }

        return $wrapChar . $str . $wrapChar;
    }

    /**
     * @param string $arg
     *
     * @return string
     */
    public static function shellQuote(string $arg): string
    {
        $quote = '';
        if (str_contains($arg, '"')) {
            $quote = "'";
        } elseif ($arg === '' || str_contains($arg, "'") || str_contains($arg, ' ')) {
            $quote = '"';
        }

        return $quote ? $arg : "$quote$arg$quote";
    }
}
