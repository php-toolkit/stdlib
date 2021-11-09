<?php declare(strict_types=1);

namespace Toolkit\Stdlib\Std;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use JsonSerializable;
use Serializable;
use Traversable;
use function count;

/**
 * Class Collection
 *
 * @author inhere
 */
class Collection implements IteratorAggregate, ArrayAccess, Serializable, Countable, JsonSerializable
{
    /**
     * The data
     *
     * @var array
     */
    protected $data = [];

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
    public function set(string $key, $value): self
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
     * @param mixed  $default The default value to return if data key does not exist
     *
     * @return mixed The key's value, or the default value
     */
    public function get(string $key, $default = null)
    {
        return $this->has($key) ? $this->data[$key] : $default;
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
     * Add item to collection
     *
     * @param array $items Key-value array of data to append to this collection
     */
    public function replace(array $items)
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
    public function toArray(): array
    {
        return $this->all();
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
     * @return mixed|null
     */
    public function remove(string $key)
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

    /********************************************************************************
     * ArrayAccess interface
     *******************************************************************************/

    /**
     * Does this collection have a given key?
     *
     * @param string $key The data key
     *
     * @return bool
     */
    public function offsetExists($key): bool
    {
        return $this->has($key);
    }

    /**
     * Get collection item for key
     *
     * @param string $key The data key
     *
     * @return mixed The key's value, or the default value
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * Set collection item
     *
     * @param string $key   The data key
     * @param mixed  $value The data value
     */
    public function offsetSet($key, $value)
    {
        $this->set($key, $value);
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
     * @return string
     */
    public function serialize(): string
    {
        return serialize($this->data);
    }

    /**
     * @param string     $serialized
     * @param bool|array $allowedClasses
     */
    public function unserialize($serialized, $allowedClasses = false)
    {
        $this->data = unserialize($serialized, ['allowed_classes' => $allowedClasses]);
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
    public function __set(string $name, $value)
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
