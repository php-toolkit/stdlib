<?php declare(strict_types=1);

namespace Toolkit\Stdlib;

use function abs;
use function ceil;
use function floor;

/**
 * Class Math
 *
 * @package Toolkit\Stdlib
 */
class Math
{
    /**
     * @param int|float $value
     *
     * @return int
     */
    public static function floor($value): int
    {
        return (int)floor((float)$value);
    }

    /**
     * @param int|float $value
     *
     * @return int
     */
    public static function ceil($value): int
    {
        return (int)ceil((float)$value);
    }

    /**
     * @param int|float $value
     *
     * @return int
     */
    public static function abs($value): int
    {
        return (int)abs($value);
    }
}
