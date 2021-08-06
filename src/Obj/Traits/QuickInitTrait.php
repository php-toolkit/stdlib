<?php declare(strict_types=1);

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
    public static function new(array $config = [])
    {
        return new static($config);
    }
}
