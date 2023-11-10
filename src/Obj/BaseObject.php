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
use Toolkit\Stdlib\Json;
use Toolkit\Stdlib\Obj;
use Toolkit\Stdlib\Obj\Traits\QuickInitTrait;

/**
 * Class AbstractObj
 *
 * @package Toolkit\Stdlib\Obj
 */
abstract class BaseObject implements JsonSerializable
{
    use QuickInitTrait;

    /**
     * Field alias mapping. [alias => field]
     *
     * @var array
     */
    protected array $_aliasMap = [];

    /**
     * Class constructor.
     *
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        if ($data) {
            $this->load($data);
        }
        $this->init();
    }

    /**
     * will call init() after constructor.
     */
    protected function init(): void
    {
        // do something...
    }

    /**
     * Batch set values.
     *
     * - Support configuration field aliases
     * - Automatically convert fields to camelCase format
     *
     * @param array $data
     * @param array $aliasMap
     */
    public function load(array $data, array $aliasMap = []): void
    {
        if ($data) {
            Obj::init($this, $data, true, $aliasMap ?: $this->_aliasMap);
        }
    }

    /**
     * Set property value. first, will try to use setter method.
     *
     * @param string $field
     * @param mixed $value
     * @return $this
     */
    public function setValue(string $field, mixed $value): static
    {
        Obj::init($this, [$field => $value], true, $this->_aliasMap);
        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return Obj::toArray($this, false, false);
    }

    /**
     * Convert to array map.
     *
     * @param bool $filter
     *
     * @return array
     */
    public function toMap(bool $filter = false): array
    {
        return $this->convToMap($filter);
    }

    /**
     * Convert to array map, will filter empty value.
     *
     * @return array
     */
    public function toCleaned(): array
    {
        return $this->convToMap(true);
    }

    /**
     * @param bool $filter
     *
     * @return array
     */
    public function toAliased(bool $filter = false): array
    {
        return $this->convToMap($filter, true);
    }

    /**
     * @var array
     */
    private array $_name2alias = [];

    /**
     * @param bool $filter filter empty value
     * @param bool $aliased key use alias.
     *
     * @return array
     */
    protected function convToMap(bool $filter = false, bool $aliased = false): array
    {
        $data = [];
        $full = get_object_vars($this);
        if ($aliased && $this->_aliasMap) {
            $this->_name2alias = array_flip($this->_aliasMap);
        }

        // filter empty value
        foreach ($full as $name => $value) {
            // skip un-exported field
            if ($name[0] === '_') {
                continue;
            }

            // use alias name.
            if ($aliased && isset($this->_name2alias[$name])) {
                $name = $this->_name2alias[$name];
            }

            // not filter empty value
            if (!$filter) {
                // value is array
                if (is_array($value)) {
                    foreach ($value as $i => $item) {
                        if ($item instanceof self) {
                            $value[$i] = $item->convToMap(false, $aliased);
                        } else {
                            $value[$i] = $item;
                        }
                    }
                    $data[$name] = $value;
                } elseif ($value instanceof self) {
                    $data[$name] = $value->convToMap(false, $aliased);
                } else {
                    $data[$name] = $value;
                }
                continue;
            }

            // filter empty value(0 or "" or null)
            if (!$value && $value !== false) {
                continue;
            }

            // value is array
            if (is_array($value)) {
                foreach ($value as $i => $item) {
                    if ($item instanceof self) {
                        $value[$i] = $item->convToMap($filter, $aliased);
                    } elseif (is_array($item)) {
                        $value[$i] = array_filter($item);
                        // on key is string, filter empty value
                        // - not handle list array. eg: value=['a', '', 'b]
                    } elseif (is_string($i) && !$item && $item !== false) {
                        unset($value[$i]);
                    }
                }

                if ($value) {
                    $data[$name] = $value;
                }
                continue;
            }

            // value is instanceof self
            if ($value instanceof self) {
                $data[$name] = $value->convToMap($filter, $aliased);
            } else {
                $data[$name] = $value;
            }
        }

        return $data;
    }

    /**
     * 转成 JSON 字符串
     *
     * @param bool $filter filter empty value
     * @param bool $aliased
     *
     * @return string
     */
    public function toJson(bool $filter = true, bool $aliased = false): string
    {
        return Json::unescaped($this->convToMap($filter, $aliased));
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toJson();
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->toMap(true);
    }

}
