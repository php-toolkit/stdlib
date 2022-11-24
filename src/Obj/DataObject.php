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
use Toolkit\Stdlib\Obj;
use Toolkit\Stdlib\Str;
use UnexpectedValueException;
use function in_array;
use const JSON_UNESCAPED_SLASHES;

/**
 * Class DataObject
 *
 * @package Toolkit\Stdlib\Obj
 */
class DataObject extends ArrayObject implements JsonSerializable
{
    /**
     * @param array $data
     *
     * @return static
     */
    public static function new(array $data = []): static
    {
        return new static($data);
    }

    /**
     * @param string $json
     *
     * @return static
     */
    public static function fromJson(string $json): static
    {
        return new static(JsonHelper::decode($json, true));
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
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        if ($this->offsetExists($key)) {
            return $this->offsetGet($key);
        }

        return $default;
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function set(string $key, mixed $value): void
    {
        $this->offsetSet($key, $value);
    }

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getValue(string $key, mixed $default = null): mixed
    {
        if ($this->offsetExists($key)) {
            return $this->offsetGet($key);
        }

        return $default;
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
     * @param array $default
     * @param string $sep
     *
     * @return array
     */
    public function getStrings(string $key, array $default = [], string $sep = ','): array
    {
        $str = $this->getString($key);

        return $str ? Str::toArray($str, $sep) : $default;
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
     * @param string $dataClass
     *
     * @return self
     */
    public function getSubObject(string $key, string $dataClass = ''): self
    {
        if ($dataClass) {
            $dataObj = new $dataClass;
            Obj::init($dataObj, $this->getArray($key));

            return $dataObj;
        }

        return new static($this->getArray($key));
    }

    /**
     * @param array $fields
     *
     * @return array
     * @deprecated please use getMulti()
     */
    public function getSome(array $fields = []): array
    {
        return $this->getMulti($fields);
    }

    /**
     * @param array $fields Restrict to return only these fields, and return all of them if it is empty
     *
     * @return array
     */
    public function getMulti(array $fields = []): array
    {
        $data = [];
        foreach ($this->getArrayCopy() as $key => $value) {
            if ($fields && !in_array($key, $fields, true)) {
                continue;
            }

            $data[$key] = $value;
        }

        return $data;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->getArrayCopy();
    }

    /**
     * @return array
     */
    public function getKeys(): array
    {
        $keys = [];
        foreach ($this as $key => $val) {
            $keys[] = $key;
        }

        return $keys;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return JsonHelper::enc($this->getArrayCopy(), JSON_UNESCAPED_SLASHES);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function __isset(string $key): bool
    {
        return $this->offsetExists($key);
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function __get(string $key): mixed
    {
        return $this->offsetGet($key);
    }

    /**
     * @param string $key
     * @param mixed $value
     *
     * @return void
     */
    public function __set(string $key, mixed $value): void
    {
         $this->offsetSet($key, $value);
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
