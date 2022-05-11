<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Util\Stream;

use ArrayIterator;
use Closure;
use JsonSerializable;
use Toolkit\Stdlib\Util\Optional;
use Traversable;
use function array_rand;
use function array_unique;
use const SORT_STRING;

/**
 * class DataStream
 *
 * @template T
 * @var $this T[]
 */
class DataStream extends ArrayIterator implements JsonSerializable
{
    private static ?DataStream $emptyObj = null;

    /**
     * @param array|Traversable $data
     *
     * @return static
     */
    public static function of(array|Traversable $data): static
    {
        return new static($data);
    }

    /**
     * @param array|Traversable $data
     *
     * @return static
     */
    public static function new(array|Traversable $data): static
    {
        return new static($data);
    }

    /**
     * @return self
     */
    public static function empty(): self
    {
        if (!self::$emptyObj) {
            self::$emptyObj = new static();
        }

        return self::$emptyObj;
    }

    /**
     * @param array $list
     *
     * @return ListStream
     */
    public static function ofList(array $list): ListStream
    {
        return ListStream::new($list);
    }

    /**
     * @param array $list
     *
     * @return MapStream
     */
    public static function ofMap(array $list): MapStream
    {
        return MapStream::new($list);
    }

    // ---------------------- helper methods ----------------------

    /**
     * @param bool $desc
     *
     * @return Closure
     */
    public static function intComparer(bool $desc = false): Closure
    {
        if ($desc) {
            return static function ($a, $b) {
                if ($a === $b) {
                    return 0;
                }

                return $a > $b ? -1 : 1;
            };
        }

        return static function ($a, $b) {
            if ($a === $b) {
                return 0;
            }

            return $a > $b ? 1 : -1;
        };
    }

    // ---------------------- middle operations ----------------------

    /**
     * @param callable(array): bool $func
     * @param mixed                 $boolExpr
     *
     * @return $this
     */
    public function filterIf(callable $func, mixed $boolExpr): self
    {
        if ($boolExpr) {
            return $this->filter($func);
        }

        return $this;
    }

    /**
     * @param callable(mixed):bool $filterFn
     *
     * @return $this
     */
    public function filter(callable $filterFn): self
    {
        $new = new static();
        foreach ($this as $item) {
            if ($filterFn($item)) {
                $new->append($item);
            }
        }

        return $new;
    }

    /**
     * @param int $number
     *
     * @return $this
     */
    public function limit(int $number): self
    {
        $idx = 0;
        $new = new static();

        foreach ($this as $item) {
            if ($idx < $number) {
                $new->append($item);
            }

            $idx++;
        }

        return $new;
    }

    /**
     * @param int $number
     *
     * @return $this
     */
    public function skip(int $number): self
    {
        $idx = 0;
        $new = new static();

        foreach ($this as $item) {
            if ($idx >= $number) {
                $new->append($item);
            }

            $idx++;
        }

        return $new;
    }

    /**
     * Distinct all values
     *
     * @return $this
     */
    public function distinct(int $flags = SORT_STRING): self
    {
        return self::of(array_unique($this->getArrayCopy(), $flags));
    }

    /**
     * @template U
     *
     * @param callable(T):U $mapper
     *
     * @return static
     */
    public function map(callable $mapper): static
    {
        $new = new static();
        foreach ($this as $item) {
            $new->append($mapper($item));
        }

        return $new;
    }

    /**
     * @template U
     *
     * @param callable(T):U $mapper
     * @param bool $boolExpr
     *
     * @return static
     */
    public function mapIf(callable $mapper, bool $boolExpr): static
    {
        if ($boolExpr) {
            return $this->map($mapper);
        }

        return $this;
    }

    /**
     * @template U
     *
     * @param callable(T):U $mapper
     * @param DataStream $stream
     *
     * @return static
     */
    public function mapTo(callable $mapper, self $stream): static
    {
        foreach ($this as $item) {
            $stream->append($mapper($item));
        }

        return $stream;
    }

    /**
     * Mapping values to MapStream
     *
     * @param callable(array|mixed): array{string,mixed} $mapper
     * @param MapStream|null $new
     *
     * @return MapStream
     */
    public function mapToMap(callable $mapper, MapStream $new = null): MapStream
    {
        $new = $new ?: new MapStream();
        foreach ($this as $item) {
            [$key, $val] = $mapper($item);
            $new->offsetSet($key, $val);
        }

        return $new;
    }

    /**
     * Mapping values to IntStream
     *
     * @param callable(T):int $mapper
     * @param IntStream|null $new
     *
     * @return IntStream
     */
    public function mapToInt(callable $mapper, IntStream $new = null): IntStream
    {
        $new = $new ?: new IntStream;
        foreach ($this as $val) {
            $new->append($mapper($val));
        }

        return $new;
    }

    /**
     * Mapping values to StringStream
     *
     * @param callable(T):string $mapper
     * @param StringStream|null $new
     *
     * @return StringStream
     */
    public function mapToString(callable $mapper, StringStream $new = null): StringStream
    {
        $new = $new ?: new StringStream;
        foreach ($this as $val) {
            $new->append($mapper($val));
        }

        return $new;
    }

    /**
     * @param callable(mixed):array $flatMapper
     *
     * @return static
     */
    public function flatMap(callable $flatMapper): static
    {
        $new = new static();
        foreach ($this as $item) {
            foreach ($flatMapper($item) as $sub) {
                $new->append($sub);
            }
        }

        return $new;
    }

    /**
     * Sort array by values
     *
     * Use compare:
     *
     * ```php
     * // return int >0, =0, <0
     * callback(mixed $a, mixed $b): int
     * ```
     *
     * @param callable|null $comparer User defined sort compare function
     *
     * @return static
     */
    public function sorted(callable $comparer = null): static
    {
        $new = self::of($this->getArrayCopy());

        $comparer ? $new->uasort($comparer) : $new->asort();

        return $new;
    }

    /**
     * Sort array by keys
     *
     * @param callable|null $comparer User defined sort compare function
     *
     * @return static
     */
    public function keySorted(callable $comparer = null): static
    {
        $new = self::of($this->getArrayCopy());

        $comparer ? $new->uksort($comparer) : $new->ksort();

        return $new;
    }

    // ---------------------- terminal operations ----------------------

    /**
     * Check if all elements are matched
     *
     * @param callable(mixed): bool $matcher
     *
     * @return bool
     */
    public function allMatch(callable $matcher): bool
    {
        $allMatch = true;
        foreach ($this as $item) {
            if (!$matcher($item)) {
                $allMatch = false;
                break;
            }
        }

        return $allMatch;
    }

    /**
     * Check if at least one element matches
     *
     * @param callable(mixed): bool $matcher
     *
     * @return bool
     */
    public function anyMatch(callable $matcher): bool
    {
        return $this->oneMatch($matcher);
    }

    /**
     * Check if at least one element matches
     *
     * @param callable(mixed): bool $matcher
     *
     * @return bool
     */
    public function oneMatch(callable $matcher): bool
    {
        $oneMatch = false;
        foreach ($this as $item) {
            if ($matcher($item)) {
                $oneMatch = true;
                break;
            }
        }

        return $oneMatch;
    }

    /**
     * Check if all elements are not matched
     *
     * @param callable(mixed): bool $matcher
     *
     * @return bool
     */
    public function noneMatch(callable $matcher): bool
    {
        $noMatch = true;
        foreach ($this as $item) {
            if ($matcher($item)) {
                $noMatch = false;
                break;
            }
        }

        return $noMatch;
    }

    /**
     * @template T
     * @return Optional<T>
     */
    public function findFirst(): Optional
    {
        $idx = 0;
        foreach ($this as $item) {
            if ($idx === 0) {
                return Optional::ofNullable($item);
            }
            $idx++;
        }

        return Optional::empty();
    }

    /**
     * @template T
     * @return Optional<T>
     */
    public function findLast(): Optional
    {
        $number = 1;
        $count  = $this->count();

        foreach ($this as $item) {
            if ($number === $count) {
                return Optional::ofNullable($item);
            }
            $number++;
        }

        return Optional::empty();
    }

    /**
     * @template T
     * @return Optional<T>
     */
    public function findAny(): Optional
    {
        return $this->findRandom();
    }

    /**
     * Find one item by given matcher
     *
     * @template T
     * @param callable(mixed): bool $matcher
     *
     * @return Optional<T>
     */
    public function findOne(callable $matcher): Optional
    {
        foreach ($this as $item) {
            if ($matcher($item)) {
                return Optional::ofNullable($item);
            }
        }

        return Optional::empty();
    }

    /**
     * @template T
     * @return Optional<T>
     */
    public function findRandom(): Optional
    {
        $key = array_rand($this->getArrayCopy());

        return Optional::ofNullable($this->offsetGet($key));
    }

    /**
     * @template T
     * @param callable(T, T): int $comparer
     *
     * @return Optional<T>
     */
    public function max(callable $comparer): Optional
    {
        return $this->sorted($comparer)->findLast();
    }

    /**
     * @template T
     * @param callable(T, T): int $comparer
     *
     * @return Optional<T>
     */
    public function min(callable $comparer): Optional
    {
        return $this->sorted($comparer)->findFirst();
    }

    /**
     * @param callable(mixed): void $handler
     */
    public function forEach(callable $handler): void
    {
        foreach ($this as $item) {
            $handler($item);
        }
    }

    /**
     * @param callable(array|mixed, int|string): array $func
     * @param array $arr
     *
     * @return array
     */
    public function eachToArray(callable $func, array $arr = []): array
    {
        foreach ($this as $idx => $item) {
            $arr[] = $func($item, $idx);
        }
        return $arr;
    }

    /**
     * @param callable(array|mixed): array{string, mixed} $func
     * @param array $map
     *
     * @return array<string, mixed>
     */
    public function eachToMap(callable $func, array $map = []): array
    {
        foreach ($this as $item) {
            [$key, $val] = $func($item);
            $map[$key] = $val;
        }

        return $map;
    }

    /**
     * @param callable $handler
     * @param ...$args
     *
     * @return mixed
     */
    public function collect(callable $handler, ...$args): mixed
    {
        // TODO
        return null;
    }

    /**
     * Alias of the method: getArrayCopy()
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->getArrayCopy();
    }

    /**
     * Alias of the method: getArrayCopy()
     *
     * @return array
     */
    public function getArray(): array
    {
        return $this->getArrayCopy();
    }

    /**
     * close and clear data.
     */
    public function close(): void
    {
        // clear all data.
    }

    /**
     * @return int
     */
    public function length(): int
    {
        return $this->count();
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    /**
     * @return bool
     */
    public function isNotEmpty(): bool
    {
        return $this->count() > 0;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->getArrayCopy();
    }
}
