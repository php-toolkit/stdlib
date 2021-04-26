<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Obj;

use JsonSerializable;
use Toolkit\Stdlib\Helper\JsonHelper;

/**
 * Class AbstractMap
 *
 * @package Toolkit\Stdlib\Obj
 */
abstract class AbstractMap implements JsonSerializable
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
     * Configurable constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        if ($config) {
            ObjectHelper::init($this, $config);
        }
    }

    /**
     * Batch set values
     *
     * @param array $data
     */
    public function load(array $data): void
    {
        ObjectHelper::init($this, $data);
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function setValue(string $key, $value): void
    {
        ObjectHelper::init($this, [$key => $value]);
    }

    /**
     * @param array $fields Restrict to return only these fields, return all of them if empty
     *
     * @return array
     */
    public function getValues(array $fields = []): array
    {
        $data = [];
        foreach ($this as $key => $value) {
            if ($fields && !in_array($key, $fields, true)) {
                continue;
            }

            $data[$key] = $value;
        }

        return $data;
    }

    /**
     * Convert to array
     *
     * @param bool  $filterEmpty
     * @param array $append
     *
     * @return array
     */
    public function toArray(bool $filterEmpty = false, array $append = []): array
    {
        $data = $this->getValues();

        if ($filterEmpty) {
            $data = array_filter($data);
        }

        // 仅在data不为空时追加数据
        if ($append && $data) {
            $data = array_merge($data, $append);
        }

        return $data;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->toJson();
    }

    /**
     * Convert to JSON string
     *
     * @param bool $filterEmpty
     *
     * @return string
     */
    public function toJson(bool $filterEmpty = false): string
    {
        return JsonHelper::enc($this->toArray($filterEmpty));
    }

    /**
     * @return mixed
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
