<?php declare(strict_types=1);

namespace Toolkit\Stdlib\Obj;

use InvalidArgumentException;

/**
 * @author inhere
 */
final class Singleton
{
    /**
     * @var array<string, object>
     */
    private static array $objMap = [];

    /**
     * @param string $id
     * @param object $obj
     */
    public static function set(string $id, object $obj): void
    {
        self::$objMap[$id] = $obj;
    }

    /**
     * get object by id
     *
     * @param string $id
     *
     * @return object|null
     */
    public static function get(string $id): ?object
    {
        return self::$objMap[$id] ?? null;
    }

    /**
     * must get the object, or throw an exception
     *
     * @param string $id
     *
     * @return object
     */
    public static function must(string $id): object
    {
        return self::$objMap[$id] ?? throw new InvalidArgumentException("object not found: $id");
    }

    /**
     * @param string $id
     *
     * @return bool
     */
    public static function has(string $id): bool
    {
        return isset(self::$objMap[$id]);
    }

    /**
     * @param string $id
     */
    public static function del(string $id): void
    {
        unset(self::$objMap[$id]);
    }

    /**
     * @return array
     */
    public static function all(): array
    {
        return self::$objMap;
    }

    /**
     * @return void
     */
    public static function clear(): void
    {
        self::$objMap = [];
    }
}
