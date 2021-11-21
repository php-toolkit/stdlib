<?php declare(strict_types=1);

namespace Toolkit\Stdlib\Util\Stream;

use function implode;

/**
 * class ListStream
 */
class MapStream extends DataStream
{
    /**
     * @param array<string, mixed> $data
     *
     * @return static
     */
    public static function new(array $data): self
    {
        return new self($data);
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function load(array $data): self
    {
        foreach ($data as $key => $value) {
            $this->offsetSet($key, $value);
        }

        return $this;
    }

    /**
     * @param callable(array): string $func
     * @param bool|mixed $apply
     *
     * @return $this
     */
    public function eachIf(callable $func, mixed $apply): self
    {
        if (!$apply) {
            return $this;
        }

        return $this->each($func);
    }

    /**
     * @param callable(mixed): mixed $func
     *
     * @return $this
     */
    public function each(callable $func): self
    {
        $new = new self();
        foreach ($this as $key => $item) {
            $new->offsetSet($key, $func($item));
        }

        return $new;
    }

    /**
     * @param callable(mixed): mixed $func
     *
     * @return $this
     */
    public function eachTo(callable $func, DataStream $new): DataStream
    {
        foreach ($this as $key => $item) {
            $item = $func($item, $key);
            $new->offsetSet($key, $item);
        }

        return $new;
    }

    /**
     * @param callable(mixed): mixed $func
     *
     * @return array<string, mixed>
     */
    public function eachToMap(callable $func): array
    {
        $map = [];
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
                $new->offsetSet($key, $filterFn($item));
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
