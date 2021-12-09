<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib;

use function gettype;
use function is_string;

/**
 * Class Type - php data type
 *
 * @package Toolkit\Stdlib
 * @see     \gettype()
 */
final class Type
{
    // ------ basic types ------

    public const INT = 'int'; // TIPS: not in gettype returns.

    public const INTEGER = 'integer';

    public const FLOAT = 'float';

    public const DOUBLE = 'double';

    public const BOOL = 'bool'; // TIPS: not in gettype returns.

    public const BOOLEAN = 'boolean';

    public const STRING = 'string';

    // ------ complex types ------
    public const ARRAY  = 'array';

    public const OBJECT = 'object';

    public const RESOURCE = 'resource';

    // TIPS: since 7.2.0
    public const RESOURCE_CLOSED = 'resource (closed)';

    // ------ other type names ------

    public const NULL = 'null';

    public const MiXED = 'mixed';

    public const CALLABLE = 'callable';

    public const UNKNOWN = 'unknown type';

    /**
     * has shorts
     */
    public const SHORT_TYPES = [
        self::BOOLEAN => self::BOOL,
        self::INTEGER => self::INT,
    ];

    /**
     * @param mixed $val
     * @param bool  $toShort
     *
     * @return string
     */
    public static function get(mixed $val, bool $toShort = false): string
    {
        $typName = gettype($val);
        if ($typName === self::UNKNOWN) {
            $typName = self::MiXED;
        } elseif ($typName === self::RESOURCE_CLOSED) {
            $typName = self::RESOURCE;
        } elseif ($toShort && isset(self::SHORT_TYPES[$typName])) {
            $typName = self::SHORT_TYPES[$typName];
        }

        return $typName;
    }

    /**
     * Get type default value.
     *
     * @param string $type
     *
     * @return array|false|float|int|string|null
     */
    public static function getDefault(string $type): float|bool|int|array|string|null
    {
        return match ($type) {
            self::INT, self::INTEGER => 0,
            self::BOOL, self::BOOLEAN => false,
            self::FLOAT => (float)0,
            self::DOUBLE => (double)0,
            self::STRING => '',
            self::ARRAY => [],
            default => null,
        };
    }

    /**
     * @param string $type
     * @param mixed  $value
     *
     * @return mixed
     */
    public static function fmtValue(string $type, mixed $value): mixed
    {
        return match ($type) {
            self::INT, self::INTEGER => (int)$value,
            self::BOOL, self::BOOLEAN => is_string($value) ? Str::toBool2($value) : (bool)$value,
            self::FLOAT => (float)$value,
            self::DOUBLE => (double)$value,
            self::STRING => (string)$value,
            self::ARRAY => (array)$value,
            default => $value
        };
    }

    /**
     * @param bool $withAlias
     *
     * @return array
     * @see \gettype()
     */
    public static function all(bool $withAlias = true): array
    {
        $types = [
            self::ARRAY,
            // self::BOOL,
            self::BOOLEAN,
            self::DOUBLE,
            self::FLOAT,
            // self::INT,
            self::INTEGER,
            self::OBJECT,
            self::STRING,
            self::RESOURCE
        ];

        if ($withAlias) {
            $types[] = self::BOOL;
            $types[] = self::INT;
        }

        return $types;
    }

    /**
     * @param bool $withAlias
     *
     * @return array
     */
    public static function scalars(bool $withAlias = true): array
    {
        $types = [
            // self::BOOL,
            self::BOOLEAN,
            self::DOUBLE,
            self::FLOAT,
            // self::INT,
            self::INTEGER,
            self::STRING
        ];

        if ($withAlias) {
            $types[] = self::BOOL;
            $types[] = self::INT;
        }

        return $types;
    }

    /**
     * @return string[]
     */
    public static function complexes(): array
    {
        return [
            self::ARRAY,
            self::OBJECT,
            self::RESOURCE,
        ];
    }
}
