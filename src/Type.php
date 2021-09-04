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

/**
 * Class Type - php data type
 *
 * @package Toolkit\PhpKit
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
    public static function get($val, bool $toShort = false): string
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
    public static function getDefault(string $type)
    {
        $value = null;
        switch ($type) {
            case self::INT:
            case self::INTEGER:
                $value = 0;
                break;
            case self::BOOL:
            case self::BOOLEAN:
                $value = false;
                break;
            case self::FLOAT:
                $value = (float)0;
                break;
            case self::DOUBLE:
                $value = (double)0;
                break;
            case self::STRING:
                $value = '';
                break;
            case self::ARRAY:
                $value = [];
                break;
        }

        return $value;
    }

    /**
     * @return array
     * @see \gettype()
     */
    public static function all(): array
    {
        return [
            self::ARRAY,
            self::BOOL,
            self::BOOLEAN,
            self::DOUBLE,
            self::FLOAT,
            self::INT,
            self::INTEGER,
            self::OBJECT,
            self::STRING,
            self::RESOURCE
        ];
    }

    /**
     * @return array
     */
    public static function scalars(): array
    {
        return [
            self::BOOL,
            self::BOOLEAN,
            self::DOUBLE,
            self::FLOAT,
            self::INT,
            self::INTEGER,
            self::STRING
        ];
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
