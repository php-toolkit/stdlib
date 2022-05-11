<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Helper;

use function abs;
use function ceil;
use function floor;
use function round;
use const PHP_ROUND_HALF_UP;

/**
 * class NumHelper
 *
 * @author inhere
 */
class NumHelper
{
    /**
     * @param float|int $value
     *
     * @return int
     */
    public static function floor(float|int $value): int
    {
        return (int)floor((float)$value);
    }

    /**
     * @param float|int $value
     *
     * @return int
     */
    public static function ceil(float|int $value): int
    {
        return (int)ceil((float)$value);
    }

    /**
     * @param float|int $value
     *
     * @return int
     */
    public static function abs(float|int $value): int
    {
        return (int)abs($value);
    }

    /**
     * @param float|int $value
     * @param int $precision
     * @param int $mode
     *
     * @return float
     */
    public static function round(float|int $value, int $precision = 0, int $mode = PHP_ROUND_HALF_UP): float
    {
        return round((float)$value, $precision, $mode);
    }

    /**
     * @param float|int $value
     *
     * @return int
     */
    public static function roundInt(float|int $value): int
    {
        return (int)round((float)$value);
    }
}
