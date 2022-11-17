<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Util;

use ArrayAccess;
use RuntimeException;
use Throwable;
use Toolkit\Stdlib\Helper\Valid;
use Toolkit\Stdlib\Util\Stream\DataStream;
use UnexpectedValueException;
use function gettype;

/**
 * class Optional - like java: java.util.Optional
 *
 * @template T
 *
 * @author inhere
 * @reference https://github.com/schmittjoh/php-option
 */
final class Optional
{
    /**
     * @var self|null
     */
    private static ?Optional $empty = null;

    /**
     * Returns an Optional with the specified present non-null value.
     *
     * @template S
     *
     * @param S $value
     *
     * @return static
     */
    public static function of(mixed $value): self
    {
        return new self(Valid::notNull($value));
    }

    /**
     * @return self
     */
    public static function empty(): self
    {
        if (!self::$empty) {
            self::$empty = new self(null);
        }

        return self::$empty;
    }

    /**
     * Creates an nullable Optional given a return value.
     * - value assert by `empty` function.
     *
     * @template S
     *
     * @param S $value
     *
     * @return static
     */
    public static function ofEmptyAble(mixed $value): self
    {
        return empty($value) ? self::empty() : self::of($value);
    }

    /**
     * Creates an nullable Optional given a return value.
     *
     * @template S
     *
     * @param S      $value     The actual return value.
     * @param S|null $nullValue The value which should be considered "None"; null by default.
     *
     * @return static
     */
    public static function nullable(mixed $value, mixed $nullValue = null): self
    {
        return $value === $nullValue ? self::empty() : self::of($value);
    }

    /**
     * @template S
     *
     * @param S      $value
     * @param S|null $nullValue
     *
     * @return static
     */
    public static function ofNullable(mixed $value, mixed $nullValue = null): self
    {
        return self::nullable($value, $nullValue);
    }

    /**
     * @template S
     *
     * @param ArrayAccess|array $array
     * @param int|string        $key
     *
     * @return static
     */
    public static function ofArrayKey(ArrayAccess|array $array, int|string $key): self
    {
        return isset($array[$key]) ? self::of($array[$key]) : self::empty();
    }

    /**
     * @param callable(): mixed $fn
     *
     * @return static
     */
    public static function ofReturn(callable $fn): self
    {
        return self::ofNullable($fn());
    }

    /**
     * @var T
     */
    private mixed $value;

    /**
     * Class constructor.
     *
     * @param T $value
     */
    private function __construct(mixed $value)
    {
        $this->value = $value;
    }

    /**
     * Returns the value if available, or throws an exception otherwise.
     *
     * @throws RuntimeException If value is not available.
     *
     * @return T
     */
    public function get()
    {
        if ($this->value === null) {
            throw new UnexpectedValueException('No value present');
        }

        return $this->value;
    }

    /**
     * @return bool
     */
    public function isNull(): bool
    {
        return $this->value === null;
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->value);
    }

    /**
     * @return bool
     */
    public function isPresent(): bool
    {
        return $this->value !== null;
    }

    /**
     * If a value is present, invoke the specified consumer with the value, otherwise do nothing.
     *
     * @param callable(T): void $consumer
     */
    public function ifPresent(callable $consumer): void
    {
        if ($this->value !== null) {
            $consumer($this->value);
        }
    }

    /**
     * If a value is present, and the value matches the given predicate,
     * return an Optional describing the value, otherwise return an empty Optional.
     *
     * @param callable(T): bool $checker
     *
     * @return Optional<T>
     */
    public function filter(callable $checker): self
    {
        if (!$this->isPresent()) {
            return $this;
        }

        return $checker($this->value) ? $this : self::empty();
    }

    /**
     * If a value is present, apply the provided mapping function to it,
     * and if the result is non-null, return an Optional describing the result.
     * Otherwise, return an empty Optional
     *
     * ```php
     *  Optional::of("foo")->map('strtoupper')->get(); // "FOO"
     * ```
     *
     * @template U
     *
     * @param callable(T):U $mapper
     *
     * @return Optional<U>
     */
    public function map(callable $mapper): self
    {
        if (!$this->isPresent()) {
            return self::empty();
        }

        return self::nullable($mapper($this->value));
    }

    /**
     * If a value is present, apply the provided {@see Optional}-bearing
     * mapping function to it, return that result, otherwise return an empty
     * {@see Optional}. This method is similar to {@see Optional::map()}, but
     * the provided mapper is one whose result is already an {@see Optional},
     * and if invoked, {@see Optional::flatMap()} does not wrap it with an additional {@see Optional}.
     *
     * @template U The type parameter to the {@see Optional} returned by
     *
     * @param callable(T):Optional<U> $flatMapper must return an {@see Optional}
     *
     * @return Optional<U>
     */
    public function flatMap(callable $flatMapper): self
    {
        if (!$this->isPresent()) {
            return self::empty();
        }

        $new = $flatMapper($this->value);
        if (!$new instanceof self) {
            throw new UnexpectedValueException('must return an object and instance of ' . self::class);
        }

        return $new;
    }

    /**
     * @param callable():Optional<T> $supplier
     *
     * @return $this
     */
    public function or(callable $supplier): self
    {
        if ($this->isPresent()) {
            return $this;
        }

        $new = $supplier();
        if (!$new instanceof self) {
            throw new UnexpectedValueException('must return an object and instance of ' . self::class);
        }

        return $new;
    }

    /**
     * If a value is present, returns a sequential Stream containing only that value,
     * otherwise returns an empty Stream.
     *
     * @param class-string $streamClass
     *
     * @return DataStream<T>
     */
    public function stream(string $streamClass = DataStream::class): DataStream
    {
        /** @var $streamClass DataStream */
        if (!$this->isPresent()) {
            return $streamClass::empty();
        }

        return $streamClass::of($this->value);
    }

    /**
     * @param T $other
     *
     * @return T
     */
    public function orElse(mixed $other)
    {
        return $this->value ?? $other;
    }

    /**
     * @param callable(): T $valCreator
     *
     * @return T
     */
    public function orElseGet(callable $valCreator)
    {
        return $this->value ?? $valCreator();
    }

    /**
     * @param null|callable():Throwable $errCreator
     *
     * @return T
     */
    public function orElseThrow(callable $errCreator = null)
    {
        if ($this->value !== null) {
            return $this->value;
        }

        if ($errCreator) {
            throw $errCreator();
        }

        throw new UnexpectedValueException('No value present');
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return 'Optional.' . ($this->value !== null ? gettype($this->value) : 'empty');
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }
}
