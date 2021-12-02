<?php declare(strict_types=1);

namespace Toolkit\Stdlib\Helper;

use InvalidArgumentException;
use RuntimeException;
use function in_array;

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
     */
    public static function notEmpty(mixed $value, string $errMsg = ''): void
    {
        if (empty($value)) {
            throw static::createEx($errMsg ?: 'Expected a non-empty value');
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
     */
    public static function notBlank(string $value, string $errMsg = ''): void
    {
        if ('' === $value) {
            throw static::createEx($errMsg ?: 'Expected a non-blank string value');
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
     * Natural number. >= 0
     *
     * @param int $value
     * @param string $errMsg
     */
    public static function naturalInt(int $value, string $errMsg = ''): void
    {
        if ($value < 0) {
            throw static::createEx($errMsg ?: 'Expected a natural number value(>=0)');
        }
    }

    /**
     * Positive integer. should > 0
     *
     * @param int $value
     * @param string $errMsg
     */
    public static function intShouldGt0(int $value, string $errMsg = ''): void
    {
        if ($value < 1) {
            throw static::createEx($errMsg ?: 'Expected a integer value and should > 0');
        }
    }

    /**
     * Positive integer. should >= 0
     *
     * @param int $value
     * @param string $errMsg
     */
    public static function intShouldGte0(int $value, string $errMsg = ''): void
    {
        if ($value < 0) {
            throw static::createEx($errMsg ?: 'Expected a integer value and should >= 0');
        }
    }

    /**
     * Positive integer. > 0
     *
     * @param int $value
     * @param string $errMsg
     */
    public static function positiveInt(int $value, string $errMsg = ''): void
    {
        if ($value < 1) {
            throw static::createEx($errMsg ?: 'Expected a positive integer value(>0)');
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
        if (!isset($data[$key]) || empty($data[$key])) {
            throw static::createEx($errMsg ?: "Data must contains key '$key' and value non-empty");
        }
    }

    // ------------- helper methods -------------

    /**
     * @param string $errMsg
     *
     * @return RuntimeException
     */
    public static function createEx(string $errMsg): RuntimeException
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
