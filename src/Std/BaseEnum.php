<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Std;

use BadMethodCallException;
use JsonSerializable;
use ReflectionClass;
use UnexpectedValueException;
use function array_key_exists;
use function array_keys;
use function array_search;
use function get_class;
use function in_array;

/**
 * class BaseEnum
 *
 * @author inhere
 * @template T
 * @link https://github.com/Elao/PhpEnums
 * @link https://github.com/myclabs/php-enum/blob/master/src/Enum.php
 */
abstract class BaseEnum implements JsonSerializable
{
    /**
     * Store existing constants in a static cache per object.
     *
     * @var array<class-string, array<string, mixed>>
     */
    protected static array $caches = [];

    /**
     * Cache of instances of the Enum class
     *
     * @var array<class-string, array<string, static>>
     */
    protected static array $instances = [];

    /**
     * @param string $name
     * @param array $args
     *
     * @return mixed
     */
    public static function __callStatic(string $name, array $args): mixed
    {
        $class = static::class;

        if (!isset(self::$instances[$class][$name])) {
            $array = static::toArray();
            if (!isset($array[$name]) && !array_key_exists($name, $array)) {
                throw new BadMethodCallException("No static method or enum constant '$name' in class $class");
            }

            return self::$instances[$class][$name] = new static($array[$name]);
        }

        return clone self::$instances[$class][$name];
    }

    /**
     * @param mixed $variable
     *
     * @return bool
     */
    final public function equals(mixed $variable = null): bool
    {
        return $variable instanceof self
            && $this->getValue() === $variable->getValue()
            && static::class === get_class($variable);
    }

    /**
     * Returns all possible values as an array
     *
     * @return array<string, mixed> Constant name in key, constant value in value
     */
    public static function toArray(): array
    {
        $class = static::class;

        if (!isset(static::$caches[$class])) {
            /** @psalm-suppress ImpureMethodCall this reflection API usage has no side-effects here */
            /** @noinspection PhpUnhandledExceptionInspection */
            $reflection = new ReflectionClass($class);

            /** @psalm-suppress ImpureMethodCall this reflection API usage has no side-effects here */
            static::$caches[$class] = $reflection->getConstants();
        }

        return static::$caches[$class];
    }

    /**
     * Returns the names (keys) of all constants in the Enum class
     *
     * @return list<string>
     */
    public static function keys(): array
    {
        return array_keys(static::toArray());
    }

    /**
     * Returns instances of the Enum class of all Enum constants
     *
     * @return array<string, static> Constant name in key, Enum instance in value
     */
    public static function values(): array
    {
        $values = [];

        /** @psalm-var T $value */
        foreach (static::toArray() as $key => $value) {
            $values[$key] = new static($value);
        }

        return $values;
    }

    /**
     * Return key for value
     *
     * @param mixed $value
     *
     * @return string|false
     */
    public static function search(mixed $value): bool|string
    {
        return array_search($value, static::toArray(), true);
    }

    /**
     * Check if is valid enum value
     *
     * @param T $value
     * @return bool
     */
    public static function isValid(mixed $value): bool
    {
        return in_array($value, static::toArray(), true);
    }

    /**
     * Asserts valid enum value
     *
     * @param T $value
     */
    public static function assertValidValue(mixed $value): void
    {
        self::mustGetKeyByValue($value);
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    protected static function mustGetKeyByValue(mixed $value): string
    {
        if (false === ($key = static::search($value))) {
            throw new UnexpectedValueException("Value '$value' is not item of the enum " . static::class);
        }

        return $key;
    }

    /**
     * Enum key, the constant name
     *
     * @var string
     */
    private string $key;

    /**
     * Enum value
     *
     * @var T
     */
    protected mixed $value;

    /**
     * Creates a new value of some type
     *
     * @psalm-pure
     * @param mixed $value
     *
     * @psalm-param T $value
     * @throws UnexpectedValueException if incompatible type is given.
     */
    public function __construct(mixed $value)
    {
        if ($value instanceof static) {
            /** @psalm-var T */
            $value = $value->getValue();
        }

        /** @psalm-suppress ImplicitToStringCast assertValidValueReturningKey returns always a string but psalm has currently an issue here */
        $this->key = static::mustGetKeyByValue($value);

        /** @psalm-var T */
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->value;
    }

    /**
     * Returns the enum key (i.e. the constant name).
     *
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return mixed
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * @return mixed
     */
    public function jsonSerialize(): mixed
    {
        return $this->getValue();
    }
}
