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

/**
 * class Valid
 *
 * @author inhere
 */
class Valid
{
    /**
     * @var string
     */
    private static string $exClass = InvalidArgumentException::class;

    /**
     * @param mixed $value
     * @param string $errMsg
     *
     * @return mixed
     */
    public static function notEmpty(mixed $value, string $errMsg = ''): mixed
    {
        if (empty($value)) {
            throw static::createEx($errMsg ?: 'Expected a non-empty value');
        }

        return $value;
    }

    /**
     * @param mixed $value
     * @param string $errMsg
     *
     * @return mixed
     */
    public static function notNull(mixed $value, string $errMsg = ''): mixed
    {
        if (null === $value) {
            throw static::createEx($errMsg ?: 'Expected a non-null value');
        }

        return $value;
    }

    /**
     * @param string $value
     * @param string $errMsg
     *
     * @return string
     */
    public static function notBlank(string $value, string $errMsg = ''): string
    {
        if ('' === $value) {
            throw static::createEx($errMsg ?: 'Expected a non-blank string value');
        }

        return $value;
    }

    /**
     * Two values should be equal, an exception will be thrown when they are not equal.
     *
     * @param mixed $value1
     * @param mixed $value2
     * @param string $errMsg
     *
     * @return bool
     */
    public static function equals(mixed $value1, mixed $value2, string $errMsg = ''): bool
    {
        if ($value1 !== $value2) {
            throw static::createEx($errMsg ?: "The $value1 should equals to $value2");
        }

        return true;
    }

    /**
     * Two values cannot be equal, an exception will be thrown when they are equal.
     *
     * @param mixed $value1
     * @param mixed $value2
     * @param string $errMsg
     *
     * @return bool
     */
    public static function notEquals(mixed $value1, mixed $value2, string $errMsg = ''): bool
    {
        if ($value1 === $value2) {
            throw static::createEx($errMsg ?: "The $value1 should not equals to $value2");
        }

        return true;
    }

    /**
     * @param bool $value
     * @param string $errMsg
     *
     * @return bool
     */
    public static function isTrue(bool $value, string $errMsg = ''): bool
    {
        if (false === $value) {
            throw static::createEx($errMsg ?: 'Expected a true value');
        }

        return true;
    }

    /**
     * @param bool $value
     * @param string $errMsg
     *
     * @return bool
     */
    public static function isFalse(bool $value, string $errMsg = ''): bool
    {
        if (true === $value) {
            throw static::createEx($errMsg ?: 'Expected a false value');
        }

        return true;
    }

    /**
     * @param mixed $needle
     * @param array $haystack
     * @param string $errMsg
     *
     * @return mixed
     */
    public static function inArray(mixed $needle, array $haystack, string $errMsg = ''): mixed
    {
        if (!in_array($needle, $haystack, true)) {
            throw static::createEx($errMsg ?: 'Expected a value in array');
        }

        return $needle;
    }

    /**
     * Value should != 0
     *
     * @param int $value
     * @param string $errMsg
     *
     * @return int
     */
    public static function notZero(int $value, string $errMsg = ''): int
    {
        if ($value === 0) {
            throw static::createEx($errMsg ?: 'Expected a non-zero integer value');
        }

        return $value;
    }

    /**
     * Natural number. >= 0
     *
     * @param int $value
     * @param string $errMsg
     *
     * @return int
     */
    public static function naturalInt(int $value, string $errMsg = ''): int
    {
        if ($value < 0) {
            throw static::createEx($errMsg ?: 'Expected a natural number value(>=0)');
        }

        return $value;
    }

    /**
     * Positive integer. > 0
     *
     * @param int $value
     * @param string $errMsg
     *
     * @return int
     */
    public static function positiveInt(int $value, string $errMsg = ''): int
    {
        if ($value < 1) {
            throw static::createEx($errMsg ?: 'Expected a positive integer value(>0)');
        }

        return $value;
    }

    /**
     * @param array $data
     * @param string $key
     * @param string $errMsg
     *
     * @return mixed
     */
    public static function arrayHasKey(array $data, string $key, string $errMsg = ''): mixed
    {
        if (!isset($data[$key])) {
            throw static::createEx($errMsg ?: "Array data must contains key '$key'");
        }

        return $data[$key];
    }

    /**
     * @param array $data
     * @param array $keys
     * @param string $errMsg
     *
     * @return array
     */
    public static function arrayHasKeys(array $data, array $keys, string $errMsg = ''): array
    {
        $values = [];
        foreach ($keys as $key) {
            if (!isset($data[$key])) {
                throw static::createEx($errMsg ?: "Array data must contains key '$key'");
            }

            $values[$key] = $data[$key];
        }

        return $values;
    }

    /**
     * @param array $data
     * @param string $key
     * @param string $errMsg
     *
     * @return mixed
     */
    public static function arrayHasNoEmptyKey(array $data, string $key, string $errMsg = ''): mixed
    {
        if (empty($data[$key])) {
            throw static::createEx($errMsg ?: "Data must contains key '$key' and value non-empty");
        }

        return $data[$key];
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
