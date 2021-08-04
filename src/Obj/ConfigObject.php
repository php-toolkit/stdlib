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

/**
 * Class ConfigObject
 *
 * @package Toolkit\Stdlib\Obj
 */
class ConfigObject extends ArrayObject
{
    /**
     * @param array $data
     *
     * @return static
     */
    public static function new(array $data = [])
    {
        return new static($data);
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
     * @return array
     */
    public function toArray(): array
    {
        return $this->getArrayCopy();
    }
}