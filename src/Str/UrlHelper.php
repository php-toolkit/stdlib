<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Str;

use InvalidArgumentException;
use function curl_exec;
use function curl_getinfo;
use function curl_init;
use function curl_setopt;
use function file_get_contents;
use function function_exists;
use function get_headers;
use function http_build_query;
use function implode;
use function mb_convert_encoding;
use function parse_url;
use function preg_match;
use function rawurlencode;
use function str_replace;
use function stream_context_create;
use function strpos;
use function trim;
use function urldecode;
use function urlencode;
use const CURLINFO_HTTP_CODE;
use const CURLOPT_CONNECTTIMEOUT;
use const CURLOPT_NOBODY;
use const CURLOPT_TIMEOUT;

/**
 * Class UrlHelper
 *
 * @package Toolkit\Stdlib\Str
 */
class UrlHelper
{
    /**
     * @param string $url the URL to be checked
     *
     * @return boolean whether the URL is relative
     */
    public static function isRelative(string $url): bool
    {
        return !str_contains($url, '//') && !str_contains($url, '://');
    }

    /**
     * @param string $str
     *
     * @return bool
     */
    public static function isUrl(string $str): bool
    {
        $rule = '/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i';

        return preg_match($rule, $str) === 1;
    }

    /**
     * @param string $str
     *
     * @return bool
     */
    public static function isGitUrl(string $str): bool
    {
        if (str_starts_with($str, 'git@')) {
            $str = 'ssh://' . $str;
        }

        $rule = '/^(http|https|ssh):\/\/(.+@)*([\w\.]+)(:[\d]+)?\/*(.*)/i';

        return preg_match($rule, $str) === 1;
    }

    /**
     * @param string $url
     *
     * @return bool
     */
    public static function simpleCheck(string $url): bool
    {
        return preg_match('/^(\w+://)(.+@)*([\w\.]+)(:[\d]+)?/*(.*)/', $url) === 1;
    }

    /**
     * @param string $url
     *
     * @return bool
     */
    public static function isFullUrl(string $url): bool
    {
        return str_starts_with($url, 'http:') || str_starts_with($url, 'https:') || str_starts_with($url, '//');
    }

    /**
     * @param string $baseUri
     * @param string ...$paths
     *
     * @return string
     */
    public static function joinPath(string $baseUri, string ...$paths): string
    {
        return $baseUri . ($paths ? '/' . implode('/', $paths) : '');
    }

    /**
     * @param string $baseUrl
     * @param array $data
     *
     * @return string
     */
    public static function build(string $baseUrl, array $data = []): string
    {
        if ($data && ($param = http_build_query($data))) {
            if ($baseUrl) {
                $baseUrl .= (strpos($baseUrl, '?') ? '&' : '?') . $param;
            } else {
                $baseUrl = $param;
            }
        }

        return $baseUrl;
    }

    /**
     * @param string $url
     *
     * @return bool
     */
    public static function canAccessed(string $url): bool
    {
        $url = trim($url);

        if (function_exists('curl_init')) {
            // use curl
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);//设置超时
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);

            if (false !== curl_exec($ch)) {
                $statusCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
                return $statusCode === 200;
            }
        } elseif (function_exists('get_headers')) {
            $headers = get_headers($url, true);
            return strpos($headers[0], '200') > 0;
        } else {
            $opts     = [
                'http' => ['timeout' => 5,]
            ];
            $context  = stream_context_create($opts);
            $resource = file_get_contents($url, false, $context);

            return (bool)$resource;
        }

        return false;
    }

    // Build arrays of values we need to decode before parsing
    protected static array $entities = [
        '%21',
        '%2A',
        '%27',
        '%28',
        '%29',
        '%3B',
        '%3A',
        '%40',
        '%26',
        '%3D',
        '%24',
        '%2C',
        '%2F',
        '%3F',
        '%23',
        '%5B',
        '%5D'
    ];

    protected static array $replacements = [
        '!',
        '*',
        "'",
        '(',
        ')',
        ';',
        ':',
        '@',
        '&',
        '=',
        '$',
        ',',
        '/',
        '?',
        '#',
        '[',
        ']'
    ];

    /**
     * @param string $url
     *
     * @return array
     */
    public static function parseUrl(string $url): array
    {
        $result = [];

        // Create encoded URL with special URL characters decoded so it can be parsed
        // All other characters will be encoded
        $encodedURL = str_replace(self::$entities, self::$replacements, urlencode($url));

        // Parse the encoded URL
        $encodedParts = parse_url($encodedURL);

        // Now, decode each value of the resulting array
        if ($encodedParts) {
            foreach ((array)$encodedParts as $key => $value) {
                $result[$key] = urldecode(str_replace(self::$replacements, self::$entities, $value));
            }
        }

        return $result;
    }

    /**
     * @param string $url
     *
     * @return array
     */
    public static function parse2(string $url): array
    {
        $info = parse_url($url);
        if ($info === false) {
            throw new InvalidArgumentException('invalid request url: ' . $url);
        }

        return array_merge([
            'scheme' => 'http',
            'host'   => '',
            'port'   => 80,
            'path'   => '/',
            'query'  => '',
        ], $info);
    }

    /**
     * url_encode form urlencode(),但是 : / ? & = ...... 几个符号不会被转码为 %3A %2F %3F %26 %3D ......
     * $url="ftp://ud03:password@www.xxx.net/中文/中文.rar";
     * $url1 =  url_encode1($url);
     * //ftp://ud03:password@www.xxx.net/%E4%B8%AD%E6%96%87/%E4%B8%AD%E6%96%87.rar
     * $url2 =  urldecode($url);
     * echo $url1.PHP_EOL.$url2.PHP_EOL;
     *
     * @param string $url
     *
     * @return string
     */
    public static function encode(string $url): string
    {
        if (!$url = trim($url)) {
            return $url;
        }

        // 若已被编码的url，将被解码，再继续重新编码
        // $decodeUrl = urldecode($url);
        $encodeUrl = urlencode($url);

        return str_replace(self::$entities, self::$replacements, $encodeUrl);
    }

    /**
     * [urlEncode 会先转换编码]
     * $url="ftp://ud03:password@www.xxx.net/中文/中文.rar";
     * $url1 =  url_encode($url);
     * //ftp://ud03:password@www.xxx.net/%C3%A4%C2%B8%C2%AD%C3%A6%C2%96%C2%87/%C3%A4%C2%B8%C2%AD%C3%A6%C2%96%C2%87.rar
     * $url2 =  urldecode($url);
     * echo $url1.PHP_EOL.$url2;
     *
     * @param string $url
     *
     * @return string
     */
    public static function encode2(string $url): string
    {
        if (!$url = trim($url)) {
            return $url;
        }

        // 若已被编码的url，将被解码，再继续重新编码
        // $url = urldecode($url);

        $encodeUrl = rawurlencode(mb_convert_encoding($url, 'utf-8'));
        // $url  = rawurlencode($url);
        return str_replace(self::$entities, self::$replacements, $encodeUrl);
    }
}
