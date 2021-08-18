<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Helper;

use InvalidArgumentException;
use RuntimeException;
use stdClass;
use function array_merge;
use function basename;
use function defined;
use function dirname;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function is_file;
use function json_decode;
use function json_encode;
use function json_last_error;
use function json_last_error_msg;
use function preg_replace;
use function trim;
use const JSON_PRETTY_PRINT;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

// Compatible with lower versions
if (!defined('JSON_THROW_ON_ERROR')) {
    define('JSON_THROW_ON_ERROR', 4194304); // since php 7.3
    // class JsonException extends RuntimeException {}
}

/**
 * Class JsonHelper
 *
 * @package Toolkit\Stdlib
 */
class JsonHelper
{
    // ----------- encode -----------

    /**
     * @param mixed $data
     * @param int   $flags
     * @param int   $depth
     *
     * @return string
     * @noinspection PhpDocMissingThrowsInspection
     */
    public static function enc($data, int $flags = 0, int $depth = 512): string
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return self::encode($data, $flags, $depth);
    }

    /**
     * Encode data to json
     *
     * @param mixed $data
     * @param int   $options
     * @param int   $depth
     *
     * @return string
     * @noinspection PhpDocMissingThrowsInspection
     */
    public static function encode($data, int $options = 0, int $depth = 512): string
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return (string)json_encode($data, \JSON_THROW_ON_ERROR | $options, $depth);
    }

    /**
     * Encode data to json with some default options
     *
     * @param mixed $data
     * @param int   $options
     * @param int   $depth
     *
     * @return string
     * @noinspection PhpDocMissingThrowsInspection
     */
    public static function encodeCN(
        $data,
        int $options = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
        int $depth = 512
    ): string {
        /** @noinspection PhpUnhandledExceptionInspection */
        return self::encode($data, $options, $depth);
    }

    /**
     * @param $data
     *
     * @return string
     * @noinspection PhpDocMissingThrowsInspection
     */
    public static function pretty($data): string
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return self::encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param mixed $data
     * @param int   $flags
     *
     * @return string
     * @noinspection PhpDocMissingThrowsInspection
     */
    public static function prettyJSON(
        $data,
        int $flags = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    ): string {
        /** @noinspection PhpUnhandledExceptionInspection */
        return self::encode($data, $flags);
    }

    /**
     * @param $data
     *
     * @return string
     * @noinspection PhpDocMissingThrowsInspection
     */
    public static function unescaped($data): string
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return self::encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param $data
     *
     * @return string
     * @noinspection PhpDocMissingThrowsInspection
     */
    public static function unescapedSlashes($data): string
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return self::encode($data, JSON_UNESCAPED_SLASHES);
    }

    /**
     * @param $data
     *
     * @return string
     * @noinspection PhpDocMissingThrowsInspection
     */
    public static function unescapedUnicode($data): string
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return self::encode($data, JSON_UNESCAPED_UNICODE);
    }

    // ----------- decode -----------

    /**
     * @param string $json
     *
     * @param bool   $assoc
     *
     * @return array|mixed
     * @noinspection PhpDocMissingThrowsInspection
     */
    public static function dec(string $json, bool $assoc = true)
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $data = json_decode($json, $assoc, 512, JSON_THROW_ON_ERROR);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new InvalidArgumentException('json_decode error: ' . json_last_error_msg());
        }

        return $data;
    }

    /**
     * Decode json
     *
     * @param string $json
     * @param bool   $assoc
     * @param int    $depth
     * @param int    $options
     *
     * @return array|object
     * @noinspection PhpDocMissingThrowsInspection
     */
    public static function decode(string $json, bool $assoc = false, int $depth = 512, int $options = 0)
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $data = json_decode($json, $assoc, $depth, JSON_THROW_ON_ERROR | $options);

        if ($errCode = json_last_error()) {
            $errMsg = json_last_error_msg();
            throw new RuntimeException("JSON decode error: $errMsg", $errCode);
        }

        return $data;
    }

    /**
     * Decode json file
     *
     * @param string $jsonFile
     * @param bool   $assoc
     * @param int    $depth
     * @param int    $options
     *
     * @return array|object
     * @noinspection PhpDocMissingThrowsInspection
     */
    public static function decodeFile(string $jsonFile, bool $assoc = false, int $depth = 512, int $options = 0)
    {
        if (!is_file($jsonFile)) {
            throw new InvalidArgumentException("json file not found: $jsonFile");
        }

        $json = file_get_contents($jsonFile);

        /** @noinspection PhpUnhandledExceptionInspection */
        return self::decode($json, $assoc, $depth, $options);
    }

    /**
     * @param string $data
     * @param bool   $toArray
     *
     * @return array|stdClass
     */
    public static function parse(string $data, bool $toArray = true)
    {
        if (is_file($data)) {
            return self::parseFile($data, $toArray);
        }

        return self::parseString($data, $toArray);
    }

    /**
     * @param string $jsonFile
     * @param bool   $toArray
     *
     * @return array|stdClass
     */
    public static function parseFile(string $jsonFile, bool $toArray = true)
    {
        if (!is_file($jsonFile)) {
            throw new InvalidArgumentException("File not found: $jsonFile");
        }

        $json = file_get_contents($jsonFile);
        return self::parseString($json, $toArray);
    }

    /**
     * @param string $json
     * @param bool   $toArray
     *
     * @return array|stdClass
     * @noinspection PhpDocMissingThrowsInspection
     */
    public static function parseString(string $json, bool $toArray = true)
    {
        if (!$json = trim($json)) {
            return $toArray ? [] : new stdClass();
        }

        $json = self::stripComments($json);
        /** @noinspection PhpUnhandledExceptionInspection */
        return self::decode($json, $toArray);
    }

    /**
     * @param string $input   JSON 数据
     * @param bool   $output  是否输出到文件， 默认返回格式化的数据
     * @param array  $options 当 $output=true,此选项有效
     *                        $options = [
     *                        'type'      => 'min' // 输出数据类型 min 压缩过的 raw 正常的
     *                        'file'      => 'xx.json' // 输出文件路径;仅是文件名，则会取输入路径
     *                        ]
     *
     * @return string
     */
    public static function format(string $input, bool $output = false, array $options = []): string
    {
        if (!$data = self::stripComments($input)) {
            return '';
        }

        if (!$output) {
            return $data;
        }

        $default = ['type' => 'min'];
        $options = array_merge($default, $options);

        if (file_exists($input) && (empty($options['file']) || !is_file($options['file']))) {
            $dir  = dirname($input);
            $name = basename($input, '.json');
            $file = $dir . '/' . $name . '.' . $options['type'] . '.json';
            // save to options
            $options['file'] = $file;
        }

        static::saveAs($data, $options['file'], $options['type']);
        return $data;
    }

    /**
     * @param string $data
     * @param string $output
     * @param array  $options
     *
     * @return bool
     */
    public static function saveAs(string $data, string $output, array $options = []): bool
    {
        $default = ['type' => 'min', 'file' => ''];
        $options = array_merge($default, $options);
        $saveDir = dirname($output);

        if (!file_exists($saveDir)) {
            throw new RuntimeException('设置的json文件输出' . $saveDir . '目录不存在！');
        }

        $name = basename($output, '.json');
        $file = $saveDir . '/' . $name . '.' . $options['type'] . '.json';

        // 去掉空白
        if ($options['type '] === 'min') {
            $data = preg_replace('/(?!\w)\s*?(?!\w)/i', '', $data);
        }

        return file_put_contents($file, $data) > 0;
    }

    /**
     * @param string $json
     *
     * @return string
     */
    public static function clearComments(string $json): string
    {
        return self::stripComments($json);
    }

    /**
     * @param string $json
     *
     * @return string
     */
    public static function stripComments(string $json): string
    {
        if (!$json = trim($json)) {
            return '';
        }

        $pattern = [
            // 去掉所有多行注释/* .... */
            '/\/\*.*?\*\/\s*/is',
            // 去掉所有单行注释//....
            '/\/\/.*?[\r\n]/is',
            // 去掉空白行
            "/(\n[\r])+/is"
        ];

        return (string)preg_replace($pattern, ['', '', "\n"], $json);
    }
}
