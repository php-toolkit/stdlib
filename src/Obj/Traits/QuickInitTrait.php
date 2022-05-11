<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Obj\Traits;

/**
 * Trait QuickInitTrait
 *
 * @package Toolkit\Stdlib\Obj\Traits
 */
trait QuickInitTrait
{
    /**
     * @param array $config
     *
     * @return static
     */
    public static function new(array $config = []): static
    {
        return new static($config);
    }
}
