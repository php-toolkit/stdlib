<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Str;

use Exception;
use Toolkit\Stdlib\Str\Traits\StringCaseHelperTrait;
use Toolkit\Stdlib\Str\Traits\StringCheckHelperTrait;
use Toolkit\Stdlib\Str\Traits\StringLengthHelperTrait;
use Toolkit\Stdlib\Str\Traits\StringSplitHelperTrait;
use Toolkit\Stdlib\Str\Traits\StringTruncateHelperTrait;
use Toolkit\Stdlib\Util\UUID;
use function array_merge;
use function array_slice;
use function base64_encode;
use function count;
use function func_get_arg;
use function func_num_args;
use function function_exists;
use function hash;
use function hex2bin;
use function implode;
use function in_array;
use function is_int;
use function is_string;
use function mb_internal_encoding;
use function mb_strlen;
use function mb_strwidth;
use function mb_substr;
use function preg_match;
use function preg_match_all;
use function preg_split;
use function random_bytes;
use function str_pad;
use function str_repeat;
use function str_replace;
use function str_word_count;
use function strip_tags;
use function strlen;
use function strpos;
use function substr;
use function trim;
use function uniqid;
use function utf8_decode;
use function utf8_encode;
use const STR_PAD_LEFT;
use const STR_PAD_RIGHT;

/**
 * Class StringHelper
 *
 * @package Toolkit\Stdlib\Str
 */
abstract class StringHelper
{
    public static $defaultEncoding = 'UTF-8';

    use StringCaseHelperTrait;
    use StringCheckHelperTrait;
    use StringLengthHelperTrait;
    use StringSplitHelperTrait;
    use StringTruncateHelperTrait;

    /**
     * @param string $str
     * @param int    $padLen
     * @param string $padStr
     * @param int    $padType
     *
     * @return string
     */
    public static function pad($str, $padLen, string $padStr = ' ', int $padType = STR_PAD_RIGHT): string
    {
        return $padLen > 0 ? str_pad((string)$str, (int)$padLen, $padStr, $padType) : (string)$str;
    }

    /**
     * @param string $str
     * @param int    $padLen
     * @param string $padStr
     *
     * @return string
     */
    public static function padLeft($str, $padLen, string $padStr = ' '): string
    {
        return $padLen > 0 ? str_pad((string)$str, (int)$padLen, $padStr, STR_PAD_LEFT) : (string)$str;
    }

    /**
     * @param string $str
     * @param int    $padLen
     * @param string $padStr
     *
     * @return string
     */
    public static function padRight($str, $padLen, string $padStr = ' '): string
    {
        return $padLen > 0 ? str_pad((string)$str, (int)$padLen, $padStr) : (string)$str;
    }

    /**
     * @param string $str
     * @param int    $padLen
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
     * gen UUID
     *
     * @param int  $version
     * @param null $node
     * @param null $ns
     *
     * @return UUID
     */
    public static function genUUID(int $version = 1, $node = null, $ns = null)
    {
        return UUID::generate($version, $node, $ns);
    }

    ////////////////////////////////////////////////////////////////////////
    /// Format
    ////////////////////////////////////////////////////////////////////////

    /**
     * [format description]
     *
     * @param       $str
     * @param array $replaceParams 用于 str_replace('search','replace',$str )
     * @param array $pregParams    用于 preg_replace('pattern','replace',$str)
     *
     * @return string [type]                [description]
     * @example
     *        $pregParams = [
     *        'xx',  //'pattern'
     *        'yy',  //'replace'
     *        ]
     *        * $pregParams = [
     *        ['xx','xx2'],  //'pattern'
     *        ['yy','yy2'],  //'replace'
     *        ]
     * @example
     *        $replaceParams = [
     *        'xx',  //'search'
     *        'yy', //'replace'
     *        ]
     *        $replaceParams = [
     *        ['xx','xx2'],  //'search'
     *        ['yy','yy2'],  //'replace'
     *        ]
     */
    public static function format($str, array $replaceParams = [], array $pregParams = []): string
    {
        if (!is_string($str) || !$str || (!$replaceParams && !$pregParams)) {
            return $str;
        }

        if ($replaceParams && count($replaceParams) === 2) {
            [$search, $replace] = $replaceParams;
            $str = str_replace($search, $replace, $str);
        }

        if ($pregParams && count($pregParams) === 2) {
            [$pattern, $replace] = $pregParams;
            $str = preg_replace($pattern, $replace, $str);
        }

        return trim($str);
    }

    /**
     * 格式化，用空格分隔各个词组
     *
     * @param string $keyword 字符串
     *
     * @return string 格式化后的字符串
     */
    public static function wordFormat(string $keyword): string
    {
        // 将全角角逗号换为空格
        $keyword = str_replace(['，', ','], ' ', $keyword);

        return preg_replace([
            // 去掉两个空格以上的
            '/\s(?=\s)/',
            // 将非空格替换为一个空格
            '/[\n\r\t]/'
        ], ['', ' '], trim($keyword));
    }

    /**
     * 缩进格式化内容，去空白/注释
     *
     * @param     $fileName
     * @param int $type
     *
     * @return mixed
     */
    public static function deleteStripSpace($fileName, $type = 0)
    {
        $data = trim(file_get_contents($fileName));
        $data = 0 === strpos($data, '<?php') ? substr($data, 5) : $data;
        $data = substr($data, -2) === '?>' ? substr($data, 0, -2) : $data;

        //去掉所有注释 换行空白保留
        if ((int)$type === 1) {
            $preg_arr = [
                '/\/\*.*?\*\/\s*/is'    // 去掉所有多行注释/* .... */
                ,
                '/\/\/.*?[\r\n]/is'    // 去掉所有单行注释//....
                ,
                '/\#.*?[\r\n]/is'      // 去掉所有单行注释 #....
            ];

            return preg_replace($preg_arr, '', $data);
        }

        $preg_arr = [
            '/\/\*.*?\*\/\s*/is'    // 去掉所有多行注释 /* .... */
            ,
            '/\/\/.*?[\r\n]/is'    // 去掉所有单行注释 //....
            ,
            '/\#.*?[\r\n]/is'      // 去掉所有单行注释 #....
            ,
            '/(?!\w)\s*?(?!\w)/is' //去掉空白行
        ];

        return preg_replace($preg_arr, '', $data);
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
        return count(preg_split('~[^\p{L}\p{N}\']+~u',$str));
    }

    /**
     * @param string $arg
     *
     * @return string
     */
    public static function shellQuote(string $arg): string
    {
        $quote = '';

        if (strpos($arg, '"') !== false) {
            $quote = "'";
        } elseif ($arg === '' || strpos($arg, "'") !== false || strpos($arg, ' ') !== false) {
            $quote = '"';
        }

        return $quote ? $arg : "$quote{$arg}$quote";
    }
}
