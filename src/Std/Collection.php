<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Std;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use JsonSerializable;
use Toolkit\Stdlib\Obj\DataObject;
use Toolkit\Stdlib\Php;
use Traversable;
use function count;
use function is_string;

/**
 * Class Collection
 *
 * @author inhere
 */
class Collection implements IteratorAggregate, ArrayAccess, Countable, JsonSerializable
{
    /**
     * The data
     *
     * @var array
     */
    protected array $data = [];

    /**
     * @param array $data
     *
     * @return static
     */
    public static function new(array $data = []): self
    {
        return new static($data);
    }

    /**
     * Create new collection
     *
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function __destruct()
    {
        $this->clear();
    }

    /********************************************************************************
     * Collection interface
     *******************************************************************************/

    /**
     * Set collection item
     *
     * @param string $key   The data key
     * @param mixed  $value The data value
     *
     * @return $this
     */
    public function set(string $key, mixed $value): self
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * @param string $name
     * @param $value
     *
     * @return $this
     */
    public function add(string $name, $value): self
    {
        if (!$this->has($name)) {
            $this->set($name, $value);
        }

        return $this;
    }

    /**
     * Get collection item for key
     *
     * @param string $key     The data key
     * @param mixed|null $default The default value to return if data key does not exist
     *
     * @return mixed The key's value, or the default value
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * @param string $key
     * @param bool   $default
     *
     * @return bool
     */
    public function getBool(string $key, bool $default = false): bool
    {
        if ($this->has($key)) {
            return (bool)$this->get($key);
        }

        return $default;
    }

    /**
     * @param string $key
     * @param int    $default
     *
     * @return int
     */
    public function getInt(string $key, int $default = 0): int
    {
        return $this->getInteger($key, $default);
    }

    /**
     * @param string $key
     * @param int    $default
     *
     * @return int
     */
    public function getInteger(string $key, int $default = 0): int
    {
        if ($this->has($key)) {
            return (int)$this->get($key);
        }

        return $default;
    }

    /**
     * @param string $key
     * @param string  $default
     *
     * @return string
     */
    public function getString(string $key, string $default = ''): string
    {
        if ($this->has($key)) {
            return (string)$this->get($key);
        }

        return $default;
    }

    /**
     * @param string $key
     * @param array $default
     *
     * @return array
     */
    public function getArray(string $key, array $default = []): array
    {
        $data = $this->get($key);

        return $data ? (array)$data : $default;
    }

    /**
     * @param string $key
     * @param array $default
     *
     * @return DataObject
     */
    public function getDataObject(string $key, array $default = []): DataObject
    {
        $data = $this->get($key);

        return DataObject::new($data ? (array)$data : $default);
    }

    /**
     * @param string $key
     * @param class-string|object $obj
     *
     * @return object
     */
    public function mapObject(string $key, string|object $obj): object
    {
        // is class string
        if (is_string($obj)) {
            $obj = new $obj();
        }

        if ($data = $this->getArray($key)) {
            Php::initObject($obj, $data);
        }

        return $obj;
    }

    /**
     * Add item to collection
     *
     * @param array $items Key-value array of data to append to this collection
     */
    public function replace(array $items): void
    {
        foreach ($items as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * @param array $keys
     *
     * @return array
     */
    public function gets(array $keys): array
    {
        $values = [];
        foreach ($keys as $name) {
            if (isset($this->data[$name])) {
                $values[$name] = $this->data[$name];
            }
        }

        return $values;
    }

    /**
     * @param array $data
     *
     * @return static
     */
    public function sets(array $data): self
    {
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }

        return $this;
    }

    /**
     * @param callable $filter
     *
     * @return static
     */
    public function reject(callable $filter): self
    {
        $data = [];

        foreach ($this as $key => $value) {
            if (!$filter($value, $key)) {
                $data[$key] = $value;
            }

            unset($this[$key]);
        }

        return new static($data);
    }

    /**
     * @param callable $callback
     *
     * @return static
     */
    public function map(callable $callback): self
    {
        $data = [];
        foreach ($this->getIterator() as $key => $value) {
            $data[$key] = $callback($value, $key);
            unset($this[$key]);
        }

        return new static($data);
    }

    /**
     * @param string $sep
     *
     * @return string
     */
    public function implode(string $sep = ','): string
    {
        return implode($sep, $this->all());
    }

    /**
     * Get all items in collection
     *
     * @return array The collection's source data
     */
    public function all(): array
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->all();
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return Php::dumpVars($this->data);
    }

    /**
     * Get collection keys
     *
     * @return array The collection's source data keys
     */
    public function keys(): array
    {
        return array_keys($this->data);
    }

    /**
     * Does this collection have a given key?
     *
     * @param string $key The data key
     *
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->data[$key]);
    }

    /**
     * Remove item from collection
     *
     * @param string $key The data key
     *
     * @return mixed
     */
    public function remove(string $key): mixed
    {
        $value = null;
        if ($this->has($key)) {
            $value = $this->data[$key];
            unset($this->data[$key]);
        }

        return $value;
    }

    /**
     * Remove all items from collection
     */
    public function clear(): void
    {
        $this->data = [];
    }

    public function reset(): void
    {
        $this->data = [];
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /********************************************************************************
     * ArrayAccess interface
     *******************************************************************************/

    /**
     * Does this collection have a given key?
     *
     * @param string $offset The data key
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return $this->has($offset);
    }

    /**
     * Get collection item for key
     *
     * @param string $offset The data key
     *
     * @return mixed The key's value, or the default value
     */
    public function offsetGet($offset): mixed
    {
        return $this->get($offset);
    }

    /**
     * Set collection item
     *
     * @param string $offset   The data key
     * @param mixed  $value The data value
     */
    public function offsetSet($offset, $value): void
    {
        $this->set($offset, $value);
    }

    /**
     * Remove item from collection
     *
     * @param string $offset The data key
     */
    public function offsetUnset($offset): void
    {
        $this->remove($offset);
    }

    /********************************************************************************
     * Countable interface
     *******************************************************************************/

    /**
     * Get number of items in collection
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->data);
    }

    /********************************************************************************
     * JsonSerializable interface
     *******************************************************************************/

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->data;
    }

    /********************************************************************************
     * Serializable interface
     *******************************************************************************/

    /**
     * @return array
     */
    public function __serialize(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function __unserialize(array $data): void
    {
        $this->data = $data;
    }

    /********************************************************************************
     * IteratorAggregate interface
     *******************************************************************************/

    /**
     * Get collection iterator
     *
     * @return ArrayIterator
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->data);
    }

    /********************************************************************************
     * Magic method
     ******************************************************************************/

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->get($name);
    }

    /**
     * @param string $name
     * @param $value
     */
    public function __set(string $name, $value): void
    {
        $this->set($name, $value);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function __isset(string $name): bool
    {
        return $this->has($name);
    }
}
