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
use LogicException;
use function in_array;
use function is_dir;
use function is_file;
use function is_resource;

/**
 * class Assert
 *
 * @author inhere
 */
class Assert
{
    /**
     * @var string
     */
    private static string $exClass = InvalidArgumentException::class;

    /**
     * @param mixed $value
     * @param string $errMsg
     * @param bool $isPrefix
     */
    public static function notEmpty(mixed $value, string $errMsg = '', bool $isPrefix = false): void
    {
        if (empty($value)) {
            if ($errMsg) {
                $errMsg = $isPrefix ? "$errMsg cannot be empty" : $errMsg;
            } else {
                $errMsg = 'Expected a non-empty value';
            }

            throw static::createEx($errMsg);
        }
    }

    /**
     * Assert value is empty, otherwise will throw ex.
     *
     * @param mixed $value
     * @param string $errMsg
     * @param bool $isPrefix
     */
    public static function empty(mixed $value, string $errMsg = '', bool $isPrefix = false): void
    {
        if (!empty($value)) {
            if ($errMsg) {
                $errMsg = $isPrefix ? "$errMsg should be empty" : $errMsg;
            } else {
                $errMsg = 'Expected a empty value';
            }

            throw static::createEx($errMsg);
        }
    }

    /**
     * @param mixed $value
     * @param string $errMsg
     */
    public static function notNull(mixed $value, string $errMsg = ''): void
    {
        if (null === $value) {
            throw static::createEx($errMsg ?: 'Expected a non-null value');
        }
    }

    /**
     * @param string $value
     * @param string $errMsg
     * @param bool $isPrefix
     */
    public static function notBlank(string $value, string $errMsg = '', bool $isPrefix = false): void
    {
        if ('' === $value) {
            if ($errMsg) {
                $errMsg = $isPrefix ? "$errMsg cannot be empty string" : $errMsg;
            } else {
                $errMsg = 'Expected a non-blank string value';
            }

            throw static::createEx($errMsg);
        }
    }

    /**
     * Two values should be equal, an exception will be thrown when they are not equal.
     *
     * @param mixed $value1
     * @param mixed $value2
     * @param string $errMsg
     */
    public static function equals(mixed $value1, mixed $value2, string $errMsg = ''): void
    {
        if ($value1 !== $value2) {
            throw static::createEx($errMsg ?: "The $value1 should equals to $value2");
        }
    }

    /**
     * Two values cannot be equal, an exception will be thrown when they are equal.
     *
     * @param mixed $value1
     * @param mixed $value2
     * @param string $errMsg
     */
    public static function notEquals(mixed $value1, mixed $value2, string $errMsg = ''): void
    {
        if ($value1 === $value2) {
            throw static::createEx($errMsg ?: "The $value1 should not equals to $value2");
        }
    }

    /**
     * @param bool $value
     * @param string $errMsg
     */
    public static function isTrue(bool $value, string $errMsg = ''): void
    {
        if (false === $value) {
            throw static::createEx($errMsg ?: 'Expected a true value');
        }
    }

    /**
     * @param bool $value
     * @param string $errMsg
     */
    public static function isFalse(bool $value, string $errMsg = ''): void
    {
        if (true === $value) {
            throw static::createEx($errMsg ?: 'Expected a false value');
        }
    }

    /**
     * @param mixed $needle
     * @param array $haystack
     * @param string $errMsg
     */
    public static function inArray(mixed $needle, array $haystack, string $errMsg = ''): void
    {
        if (!in_array($needle, $haystack, true)) {
            throw static::createEx($errMsg ?: 'Expected a value in array');
        }
    }

    /**
     * Value should != 0
     *
     * @param int $value
     * @param string $errMsg
     */
    public static function notZero(int $value, string $errMsg = ''): void
    {
        if ($value === 0) {
            throw static::createEx($errMsg ?: 'Expected a non-zero integer value');
        }
    }

    /**
     * Positive integer. should > 0
     *
     * @param int $value
     * @param string $errMsg
     * @param bool $isPrefix
     */
    public static function intShouldGt0(int $value, string $errMsg = '', bool $isPrefix = false): void
    {
        if ($value < 1) {
            if ($errMsg) {
                $errMsg = $isPrefix ? "$errMsg should > 0" : $errMsg;
            } else {
                $errMsg = 'Expected a integer value and should > 0';
            }

            throw static::createEx($errMsg);
        }
    }

    /**
     * Positive integer. should >= 0
     *
     * @param int $value
     * @param string $errMsg
     * @param bool $isPrefix
     */
    public static function intShouldGte0(int $value, string $errMsg = '', bool $isPrefix = false): void
    {
        if ($value < 0) {
            if ($errMsg) {
                $errMsg = $isPrefix ? "$errMsg should >= 0" : $errMsg;
            } else {
                $errMsg = 'Expected a integer value and should >= 0';
            }

            throw static::createEx($errMsg);
        }
    }

    /**
     * Positive integer. should > 0
     *
     * @param int $value
     * @param string $errMsg
     * @param bool $isPrefix
     */
    public static function positiveInt(int $value, string $errMsg = '', bool $isPrefix = false): void
    {
        if ($value < 1) {
            if ($errMsg) {
                $errMsg = $isPrefix ? "$errMsg should > 0" : $errMsg;
            } else {
                $errMsg = 'Expected a positive integer value(>0)';
            }

            throw static::createEx($errMsg);
        }
    }

    /**
     * Natural number. should >= 0
     *
     * @param int $value
     * @param string $errMsg
     * @param bool $isPrefix
     */
    public static function naturalInt(int $value, string $errMsg = '', bool $isPrefix = false): void
    {
        if ($value < 0) {
            if ($errMsg) {
                $errMsg = $isPrefix ? "$errMsg should >= 0" : $errMsg;
            } else {
                $errMsg = 'Expected a natural number value(>=0)';
            }

            throw static::createEx($errMsg);
        }
    }

    /**
     * @param array $data
     * @param string $key
     * @param string $errMsg
     */
    public static function arrayHasKey(array $data, string $key, string $errMsg = ''): void
    {
        if (!isset($data[$key])) {
            throw static::createEx($errMsg ?: "Array data must contains key '$key'");
        }
    }

    /**
     * @param array $data
     * @param array $keys
     * @param string $errMsg
     */
    public static function arrayHasKeys(array $data, array $keys, string $errMsg = ''): void
    {
        foreach ($keys as $key) {
            if (!isset($data[$key])) {
                throw static::createEx($errMsg ?: "Array data must contains key '$key'");
            }
        }
    }

    /**
     * @param array $data
     * @param string $key
     * @param string $errMsg
     */
    public static function arrayHasNoEmptyKey(array $data, string $key, string $errMsg = ''): void
    {
        if (empty($data[$key])) {
            throw static::createEx($errMsg ?: "Data must contains key '$key' and value non-empty");
        }
    }

    /**
     * @param string $path
     * @param string $errMsg
     *
     * @return void
     */
    public static function isDir(string $path, string $errMsg = ''): void
    {
        if (!$path || !is_dir($path)) {
            throw static::createEx($errMsg ?: "No such dir: $path");
        }
    }

    /**
     * @param string $path
     * @param string $errMsg
     *
     * @return void
     */
    public static function isFile(string $path, string $errMsg = ''): void
    {
        if (!$path || !is_file($path)) {
            throw static::createEx($errMsg ?: "No such file: $path");
        }
    }

    /**
     * @param mixed|resource $res
     * @param string $errMsg
     *
     * @return void
     */
    public static function isResource($res, string $errMsg = ''): void
    {
        if (!is_resource($res)) {
            throw static::createEx($errMsg ?: 'Excepted an resource');
        }
    }

    // ------------- helper methods -------------

    /**
     * @param string $errMsg
     *
     * @return LogicException
     */
    public static function createEx(string $errMsg): LogicException
    {
        return new self::$exClass($errMsg);
    }

    /**
     * @return string
     */
    public static function getExClass(): string
    {
        return self::$exClass;
    }

    /**
     * @param string $exClass
     */
    public static function setExClass(string $exClass): void
    {
        if ($exClass) {
            self::$exClass = $exClass;
        }
    }
}
