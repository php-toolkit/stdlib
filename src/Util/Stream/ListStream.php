<?php declare(strict_types=1);

namespace Toolkit\Stdlib\Util\Stream;

use function implode;

/**
 * class ListStream
 */
class ListStream extends DataStream
{
    /**
     * @param array $data
     *
     * @return static
     */
    public static function new(array $data): self
    {
        return new self($data);
    }

    protected function createNext(): DataStream
    {
        return new self();
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
     * @param callable(array|mixed): string $func
     *
     * @return $this
     */
    public function each(callable $func): self
    {
        $new = new self();
        foreach ($this as $item) {
            $new->append($func($item));
        }

        return $new;
    }

    /**
     * @param callable(array|mixed): string $func
     * @param DataStream $new
     *
     * @return DataStream
     */
    public function eachTo(callable $func, DataStream $new): DataStream
    {
        foreach ($this as $item) {
            $new->append($func($item));
            // $new->offsetSet($key, $func($item));
        }

        return $new;
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
     * @param callable(array|mixed): array $func
     * @param MapStream $new
     *
     * @return MapStream
     */
    public function eachToMapStream(callable $func, MapStream $new): MapStream
    {
        foreach ($this as $item) {
            [$key, $val] = $func($item);
            $new->offsetSet($key, $val);
        }

        return $new;
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
     * @param string $sep
     *
     * @return string
     */
    public function join(string $sep = ','): string
    {
        return $this->implode($sep);
    }

    /**
     * @param string $sep
     *
     * @return string
     */
    public function implode(string $sep = ','): string
    {
        return implode($sep, $this->getArrayCopy());
    }

    // public function prepend(string $value): self
    // {
    //     return $this;
    // }

    public function append($value): self
    {
        parent::append($value);
        return $this;
    }
}
