<?php declare(strict_types=1);

namespace Toolkit\Stdlib\Util\Stream;

use ArrayIterator;
use Closure;
use Toolkit\Stdlib\Util\Optional;
use function array_rand;
use function array_unique;
use const SORT_STRING;

/**
 * class DataStream
 *
 * @template T
 * @var $this T[]
 */
class DataStream extends ArrayIterator
{
    /**
     * @param array $data
     *
     * @return self
     */
    public static function of(array $data): self
    {
        return new static($data);
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
     * @return $this
     */
    public function distinct(int $flags = SORT_STRING): self
    {
        return self::of(array_unique($this->getArrayCopy(), $flags));
    }

    /**
     * @template T
     * @template U
     *
     * @param callable(T):U $mapper
     *
     * @return $this
     */
    public function map(callable $mapper): self
    {
        $new = new static();

        foreach ($this as $item) {
            $new->append($mapper($item));
        }

        return $new;
    }

    /**
     * @param callable(mixed):array $flatMapper
     *
     * @return $this
     */
    public function flatMap(callable $flatMapper): self
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
     * @return $this
     */
    public function sorted(callable $comparer = null): self
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
     * @return $this
     */
    public function keySorted(callable $comparer = null): self
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
        $count = $this->count();

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

    public function collect(callable $handler, ...$args): mixed
    {
        // TODO
        return null;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->getArrayCopy();
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
}
