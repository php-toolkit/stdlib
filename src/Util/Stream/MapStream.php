<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Util\Stream;

use InvalidArgumentException;
use function implode;

/**
 * class ListStream
 */
class MapStream extends DataStream
{
    /**
     * @param mixed $value
     *
     * @return void
     */
    public function append(mixed $value): void
    {
        throw new InvalidArgumentException('not all call append on MapStream');
    }

    /**
     * @param callable(mixed, string): mixed $mapper
     *
     * @return $this
     */
    public function map(callable $mapper): static
    {
        $new = new static();
        foreach ($this as $key => $item) {
            $item = $mapper($item, $key);
            $new->offsetSet($key, $item);
        }

        return $new;
    }

    /**
     * @param callable(mixed): mixed $func
     * @param array $map
     *
     * @return array<string, mixed>
     */
    public function eachToMap(callable $func, array $map = []): array
    {
        foreach ($this as $key => $item) {
            $map[$key] = $func($item);
        }

        return $map;
    }

    /**
     * @param callable(mixed): bool $filterFn
     *
     * @return $this
     */
    public function filter(callable $filterFn): self
    {
        $new = new static();
        foreach ($this as $key => $item) {
            if ($filterFn($item)) {
                $new->offsetSet($key, $item);
            }
        }

        return $new;
    }

    /**
     * @param string $sep
     *
     * @return string
     */
    public function joinValues(string $sep = ','): string
    {
        return $this->implodeValues($sep);
    }

    /**
     * @param string $sep
     *
     * @return string
     */
    public function implodeValues(string $sep = ','): string
    {
        return implode($sep, $this->getArrayCopy());
    }
}
