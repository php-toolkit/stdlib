<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Obj;

use ArrayObject;
use JsonSerializable;
use Toolkit\Stdlib\Helper\JsonHelper;
use UnexpectedValueException;

/**
 * Class ConfigObject
 *
 * @package Toolkit\Stdlib\Obj
 */
class DataObject extends ArrayObject  implements JsonSerializable
{
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
     * @param array $data
     * @param bool  $override
     */
    public function load(array $data, bool $override = false): void
    {
        if ($override) {
            $this->override($data);
            return;
        }

        foreach ($data as $key => $val) {
            $this->offsetSet($key, $val);
        }
    }

    /**
     * @param array $data
     */
    public function override(array $data): void
    {
        $this->exchangeArray($data);
    }

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed|null
     */
    public function get(string $key, $default = null)
    {
        return $this[$key] ?? $default;
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function set(string $key, $value): void
    {
        $this->offsetSet($key, $value);
    }

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed|null
     */
    public function getValue(string $key, $default = null)
    {
        return $this[$key] ?? $default;
    }

    /**
     * @param string $key
     * @param bool   $default
     *
     * @return bool
     */
    public function getBool(string $key, bool $default = false): bool
    {
        if ($this->offsetExists($key)) {
            return (bool)$this->offsetGet($key);
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
        if ($this->offsetExists($key)) {
            return (int)$this->offsetGet($key);
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
        if ($this->offsetExists($key)) {
            return (string)$this->offsetGet($key);
        }

        return $default;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function getNotBlock(string $key): string
    {
        $val = $this->get($key, '');
        if ($val === '') {
            throw new UnexpectedValueException("the $key value cannot be empty");
        }

        return $val;
    }

    /**
     * @param string $key
     * @param array  $default
     *
     * @return array
     */
    public function getArray(string $key, array $default = []): array
    {
        if ($this->offsetExists($key)) {
            return (array)$this->offsetGet($key);
        }

        return $default;
    }

    /**
     * @param string $key
     *
     * @return self
     */
    public function getSubObject(string $key): self
    {
        return new self($this->getArray($key));
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->getArrayCopy();
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return JsonHelper::enc($this->getArrayCopy(), JSON_THROW_ON_ERROR);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * @return mixed
     */
    public function jsonSerialize(): array
    {
        return $this->getArrayCopy();
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }
}
