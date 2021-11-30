<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Str;

use function array_key_exists;
use function html_entity_decode;
use function htmlentities;
use function htmlspecialchars;
use function htmlspecialchars_decode;
use function in_array;
use function is_array;
use function is_string;
use function json_encode;
use function preg_match_all;
use function preg_replace;
use function strpos;

/**
 * Class HtmlHelper
 *
 * @package Toolkit\Stdlib\Str
 */
class HtmlHelper
{
    /**
     * Encodes special characters into HTML entities.
     *
     * @param string $text data to be encoded
     * @param string $charset
     *
     * @return string the encoded data
     * @see http://www.php.net/manual/en/function.htmlspecialchars.php
     */
    public static function encode(string $text, string $charset = 'utf-8'): string
    {
        return htmlspecialchars($text, ENT_QUOTES, $charset);
    }

    /**
     * This is the opposite of {@link encode()}.
     *
     * @param string $text data to be decoded
     *
     * @return string the decoded data
     * @see http://www.php.net/manual/en/function.htmlspecialchars-decode.php
     */
    public static function decode(string $text): string
    {
        return htmlspecialchars_decode($text, ENT_QUOTES);
    }

    /**
     * @form yii1
     *
     * @param array $data data to be encoded
     * @param string $charset
     *
     * @return array the encoded data
     * @see  http://www.php.net/manual/en/function.htmlspecialchars.php
     */
    public static function encodeArray(array $data, string $charset = 'utf-8'): array
    {
        $d = [];

        foreach ($data as $key => $value) {
            if (is_string($key)) {
                $key = htmlspecialchars($key, ENT_QUOTES, $charset);
            }

            if (is_string($value)) {
                $value = htmlspecialchars($value, ENT_QUOTES, $charset);
            } elseif (is_array($value)) {
                $value = static::encodeArray($value);
            }

            $d[$key] = $value;
        }

        return $d;
    }

    /**
     * html代码转义
     * htmlspecialchars 只转化这几个html [ & ' " < > ] 代码 --> [ &amp; &quot;  ]，
     * 而 htmlentities 却会转化所有的html代码，连同里面的它无法识别的中文字符也会转化。
     * 一般使用 htmlspecialchars 就足够了，要使用 htmlentities 时，要注意为第三个参数传递正确的编码。
     *  htmlentities() <--> html_entity_decode() — 将特殊的 HTML 实体转换回普通字符
     *  htmlspecialchars() <--> htmlspecialchars_decode() — 将特殊的 HTML 实体转换回普通字符
     * ENT_COMPAT ENT_QUOTES ENT_NOQUOTES ENT_HTML401 ENT_XML1 ENT_XHTML ENT_HTML5
     *
     * @param        $data
     * @param int    $type
     * @param string $encoding
     *
     * @return array|string
     */
    public static function escape($data, int $type = 0, string $encoding = 'UTF-8'): array|string
    {
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $data[$k] = self::escape($data, $type, $encoding);
            }

            return $data;
        }

        // 默认使用  htmlspecialchars()
        if (!$type) {
            $data = htmlspecialchars($data, \ENT_QUOTES, $encoding);
        } else {
            $data = htmlentities($data, \ENT_QUOTES, $encoding);
        }

        //如‘&#x5FD7;’这样的16进制的html字符，为了防止这样的字符被错误转译，使用正则进行匹配，把这样的字符又转换回来。
        if (strpos($data, '&#')) {
            $data = preg_replace('/&((#(\d{3,5}|x[a-fA-F0-9]{4}));)/', '&\\1', $data);
        }

        return $data;
    }

    /**
     * 去掉html转义
     *
     * @param array|string $data
     * @param int $type
     * @param string       $encoding
     *
     * @return array|string
     */
    public static function unescap(array|string $data, int $type = 0, string $encoding = 'UTF-8'): array|string
    {
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $data[$k] = self::unescap($data, $type, $encoding);
            }
        } elseif (!$type) {// 默认使用  htmlspecialchars_decode()
            $data = htmlspecialchars_decode($data, \ENT_QUOTES);
        } else {
            $data = html_entity_decode($data, \ENT_QUOTES, $encoding);
        }

        return $data;
    }

    /**
     * Strip img-tags from string
     *
     * @param string $string Sting to be cleaned.
     *
     * @return  string  Cleaned string
     */
    public static function stripImages(string $string): string
    {
        return preg_replace('#(<[/]?img.*>)#U', '', $string);
    }

    /**
     * Strip iframe-tags from string
     *
     * @param string $string Sting to be cleaned.
     *
     * @return  string  Cleaned string
     */
    public static function stripIframes(string $string): string
    {
        return preg_replace('#(<[/]?iframe.*>)#U', '', $string);
    }

    /**
     * stripScript
     *
     * @param string $string
     *
     * @return string
     */
    public static function stripScript(string $string): string
    {
        return preg_replace('/<script[^>]*>.*?</script>/si', '', $string);
    }

    /**
     * stripStyle
     *
     * @param string $string
     *
     * @return string
     */
    public static function stripStyle(string $string): string
    {
        return preg_replace('/<style[^>]*>.*?</style>/si', '', $string);
    }

    /**
     * @param string    $html
     * @param bool|true $onlySrc
     *
     * @return array
     */
    public static function matchImages(string $html, bool $onlySrc = true): array
    {
        // $preg = '/<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*>/i';
        $preg = '/<img.+src=\"(:?.+.+\.(?:jpg|gif|bmp|bnp|png)\"?).+>/i';

        if (!preg_match_all($preg, trim($html), $images)) {
            return [];
        }

        if ($onlySrc) {
            return array_key_exists(1, $images) ? $images[1] : [];
        }

        return $images;
    }

    /**
     * @param string $html
     *
     * @return string
     */
    public static function minify(string $html): string
    {
        $search  = [
            '/(?:(?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:(?<!\:|\\\|\')\/\/.*))/',
            '/\n/',
            '/\>[^\S ]+/s',
            '/[^\S ]+\</s',
            '/(\s)+/s'
        ];
        $replace = [' ', ' ', '>', '<', '\\1'];

        return preg_replace($search, $replace, $html);
    }

    /**
     * Display a var dump in firebug console
     *
     * @param mixed  $object Object to display
     * @param string $type
     */
    public static function fd(mixed $object, string $type = 'log'): void
    {
        $types = ['log', 'debug', 'info', 'warn', 'error', 'assert'];

        if (!in_array($type, $types, true)) {
            $type = 'log';
        }

        $data = json_encode($object);

        echo '<script type="text/javascript">console.' . $type . '(' . $data . ');</script>';
    }
}
