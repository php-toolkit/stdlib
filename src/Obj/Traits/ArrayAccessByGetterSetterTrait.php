<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Obj\Traits;

use function method_exists;
use function property_exists;

/**
 * Class TraitArrayAccess
 *
 * @package Toolkit\Stdlib\Obj\Traits
 * ```
 * class A implements \ArrayAccess
 * {
 *     use ArrayAccessByGetterSetterTrait;
 * }
 * ```
 */
trait ArrayAccessByGetterSetterTrait
{
    /** @var bool */
    // protected $__strict__ = false;

    /**
     * Checks whether an offset exists in the iterator.
     *
     * @param mixed $offset The array offset.
     *
     * @return  boolean  True if the offset exists, false otherwise.
     */
    public function offsetExists($offset): bool
    {
        return property_exists($this, $offset);
    }

    /**
     * Gets an offset in the iterator.
     *
     * @param mixed $offset The array offset.
     *
     * @return  mixed  The array value if it exists, null otherwise.
     */
    public function offsetGet($offset)
    {
        $getter = 'get' . ucfirst($offset);

        if (method_exists($this, $getter)) {
            return $this->$getter();
        }

        return null;
    }

    /**
     * Sets an offset in the iterator.
     *
     * @param mixed $offset The array offset.
     * @param mixed $value  The array value.
     */
    public function offsetSet($offset, $value): void
    {
        $setter = 'set' . ucfirst($offset);

        if (method_exists($this, $setter)) {
            $this->$setter($value);
        }
    }

    /**
     * Unset an offset in the iterator.
     *
     * @param mixed $offset The array offset.
     *
     * @return  void
     */
    public function offsetUnset($offset): void
    {
        // unset($this->$offset);
    }
}
