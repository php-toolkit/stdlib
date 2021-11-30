<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Obj\Traits;

use function property_exists;

/**
 * Class TraitArrayAccess
 *
 * @package Toolkit\Stdlib\Obj\Traits
 * ```
 * class A implements \ArrayAccess
 * {
 *     use ArrayAccessByPropertyTrait;
 * }
 * ```
 */
trait ArrayAccessByPropertyTrait
{
    /**
     * Checks whether an offset exists in the iterator.
     *
     * @param mixed $offset The array offset.
     *
     * @return  boolean  True if the offset exists, false otherwise.
     */
    public function offsetExists(mixed $offset): bool
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
    public function offsetGet(mixed $offset): mixed
    {
        return $this->$offset;
    }

    /**
     * Sets an offset in the iterator.
     *
     * @param mixed $offset The array offset.
     * @param mixed $value  The array value.
     *
     * @return  void
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->$offset = $value;
    }

    /**
     * Unset an offset in the iterator.
     *
     * @param mixed $offset The array offset.
     *
     * @return  void
     */
    public function offsetUnset(mixed $offset): void
    {
        // unset($this->$offset);
    }
}
