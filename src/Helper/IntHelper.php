<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Helper;

use function is_array;
use function is_int;

/**
 * Class IntHelper
 *
 * @link http://cn2.php.net/manual/zh/function.pack.php#119402
 */
class IntHelper
{
    /**
     * @param int $val1
     * @param int $val2
     *
     * @return int
     */
    public static function getMax(int $val1, int $val2): int
    {
        return $val1 > $val2 ? $val1 : $val2;
    }

    /**
     * @param int $val1
     * @param int $val2
     *
     * @return int
     */
    public static function getMin(int $val1, int $val2): int
    {
        return $val1 < $val2 ? $val1 : $val2;
    }

    // ----- http://cn2.php.net/manual/zh/function.pack.php#119402

    /**
     * @param $i
     *
     * @return false|mixed|string
     */
    public static function int8($i): mixed
    {
        return is_int($i) ? pack('c', $i) : unpack('c', $i)[1];
    }

    public static function uInt8($i)
    {
        return is_int($i) ? pack('C', $i) : unpack('C', $i)[1];
    }

    public static function int16($i)
    {
        return is_int($i) ? pack('s', $i) : unpack('s', $i)[1];
    }

    public static function uint16($i, $endianness = false)
    {
        $f = is_int($i) ? 'pack' : 'unpack';

        if ($endianness === true) {
            // big-endian
            $i = $f('n', $i);
        } elseif ($endianness === false) {
            // little-endian
            $i = $f('v', $i);
        } elseif ($endianness === null) {
            // machine byte order
            $i = $f('S', $i);
        }

        return is_array($i) ? $i[1] : $i;
    }

    public static function int32($i)
    {
        return is_int($i) ? pack('l', $i) : unpack('l', $i)[1];
    }

    public static function uint32($i, $endianness = false)
    {
        $f = is_int($i) ? 'pack' : 'unpack';

        if ($endianness === true) {
            // big-endian
            $i = $f('N', $i);
        } elseif ($endianness === false) {
            // little-endian
            $i = $f('V', $i);
        } elseif ($endianness === null) {
            // machine byte order
            $i = $f('L', $i);
        }

        return is_array($i) ? $i[1] : $i;
    }

    public static function int64($i)
    {
        return is_int($i) ? pack('q', $i) : unpack('q', $i)[1];
    }

    public static function uint64($i, $endianness = false)
    {
        $f = is_int($i) ? 'pack' : 'unpack';

        if ($endianness === true) {
            // big-endian
            $i = $f('J', $i);
        } elseif ($endianness === false) {
            // little-endian
            $i = $f('P', $i);
        } elseif ($endianness === null) {
            // machine byte order
            $i = $f('Q', $i);
        }

        return is_array($i) ? $i[1] : $i;
    }
}
