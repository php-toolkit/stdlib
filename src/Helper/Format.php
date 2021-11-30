<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Helper;

use function count;
use function date;
use function explode;
use function floor;
use function is_numeric;
use function log;
use function microtime;
use function round;
use function sprintf;
use function strlen;
use function strtolower;
use function substr;

/**
 * Class Format
 *
 * @package Toolkit\Stdlib\Helper
 */
class Format
{
    public const TIME_FORMATS = [
        [0, '< 1 sec'],
        [1, '1 sec'],
        [2, 'secs', 1],
        [60, '1 min'],
        [120, 'mins', 60],
        [3600, '1 hr'],
        [7200, 'hrs', 3600],
        [86400, '1 day'],
        [172800, 'days', 86400],
    ];

    /**
     * format timestamp to how long ago
     *
     * @param int $secs
     *
     * @return string
     */
    public static function howLongAgo(int $secs): string
    {
        return self::beforeTime($secs);
    }

    /**
     * format Time
     *
     * @param int $secs
     *
     * @return string
     */
    public static function beforeTime(int $secs): string
    {
        $rowNum = count(self::TIME_FORMATS);
        foreach (self::TIME_FORMATS as $index => $format) {
            if ($secs >= $format[0]) {
                $nextSecs = self::TIME_FORMATS[$index + 1][0] ?? 0;
                if ($secs < $nextSecs || $index === $rowNum - 1) {
                    if (2 === count($format)) {
                        return $format[1];
                    }

                    return floor($secs / $format[2]) . ' ' . $format[1];
                }
            }
        }

        return date('Y-m-d H:i:s', $secs);
    }

    /**
     * @param string|float|null $mTime value is microtime(1)
     *
     * @return string
     */
    public static function microTime(string|float $mTime = null): string
    {
        if (!$mTime) {
            $mTime = microtime(true);
        }

        [$ts, $ms] = explode('.', sprintf('%.4f', $mTime));

        return date('Y/m/d H:i:s', (int)$ts) . '.' . $ms;
    }

    /**
     * format memory
     *
     * ```
     * Format::memory(memory_get_usage(true));
     * ```
     *
     * @param int|float $memory
     *
     * @return string
     */
    public static function memory(int|float $memory): string
    {
        if ($memory >= 1024 * 1024 * 1024) {
            return sprintf('%.1f GiB', $memory / 1024 / 1024 / 1024);
        }

        if ($memory >= 1024 * 1024) {
            return sprintf('%.1f MiB', $memory / 1024 / 1024);
        }

        if ($memory >= 1024) {
            return sprintf('%d KiB', $memory / 1024);
        }

        return sprintf('%d B', $memory);
    }

    /**
     * Format size
     *
     * ```php
     * Format::size(memory_get_usage(true));
     * ```
     *
     * @param int|float $size
     *
     * @return string
     */
    public static function size(int|float $size): string
    {
        if ($size >= 1024 * 1024 * 1024) {
            return sprintf('%.1f Gb', $size / 1024 / 1024 / 1024);
        }

        if ($size >= 1024 * 1024) {
            return sprintf('%.1f Mb', $size / 1024 / 1024);
        }

        if ($size >= 1024) {
            return sprintf('%d Kb', $size / 1024);
        }

        return sprintf('%d b', $size);
    }

    /**
     * Format a number into a human readable format
     * e.g. 24962496 => 23.81M
     *
     * @param int|float $size
     * @param int       $precision
     *
     * @return string
     */
    public static function bytes(int|float $size, int $precision = 2): string
    {
        if ($size < 1) {
            return '0b';
        }

        $base      = log($size) / log(1024);
        $suffixes  = ['b', 'k', 'M', 'G', 'T'];
        $floorBase = floor($base);

        return round(1024 ** ($base - $floorBase), $precision) . $suffixes[(int)$floorBase];
    }

    /**
     * Convert a shorthand byte value from a PHP configuration directive to an integer value
     *
     * @param string|int|numeric $value value to convert
     *
     * @return int
     */
    public static function convertBytes(int|float|string $value): int
    {
        if (is_numeric($value)) {
            return $value;
        }

        $len  = strlen($value);
        $qty  = (int)substr($value, 0, $len - 1);
        $unit = strtolower(substr($value, $len - 1));
        $qty  *= match ($unit) {
            'k' => 1024,
            'm' => 1048576,
            'g' => 1073741824,
        };

        return $qty;
    }
}
