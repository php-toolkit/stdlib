<?php declare(strict_types=1);

namespace Toolkit\Stdlib\Arr\Traits;

use ArrayObject;
use stdClass;
use Toolkit\Stdlib\Arr\ArrConst;
use Toolkit\Stdlib\Helper\DataHelper;
use Traversable;
use function implode;
use function is_array;
use function is_numeric;
use function is_object;
use function is_scalar;
use function is_string;
use function sprintf;
use function trim;

/**
 * trait ArrayConvertTrait
 *
 * @author inhere
 * @date 2022/12/30
 */
trait ArrayConvertTrait
{
    /**
     * @param mixed $array
     *
     * @return Traversable
     */
    public static function toIterator(mixed $array): Traversable
    {
        if (!$array instanceof Traversable) {
            $array = new ArrayObject((array)$array);
        }

        return $array;
    }

    /**
     * array data to object
     *
     * @param iterable $array
     * @param string   $class
     *
     * @return mixed
     */
    public static function toObject(iterable $array, string $class = stdClass::class): mixed
    {
        $object = new $class;

        foreach ($array as $name => $value) {
            $name = trim($name);

            if (!$name || is_numeric($name)) {
                continue;
            }

            $object->$name = is_array($value) ? self::toObject($value) : $value;
        }

        return $object;
    }

    /**
     * @param array $arr
     *
     * @return array
     */
    public static function valueToLower(array $arr): array
    {
        return self::changeValueCase($arr, false);
    }

    /**
     * @param array $arr
     *
     * @return array
     */
    public static function valueToUpper(array $arr): array
    {
        return self::changeValueCase($arr);
    }

    /**
     * 将数组中的值全部转为大写或小写
     *
     * @param iterable $arr
     * @param bool           $toUpper
     *
     * @return array
     */
    public static function changeValueCase(iterable $arr, bool $toUpper = true): array
    {
        $function = $toUpper ? 'strtoupper' : 'strtolower';
        $newArr   = []; //格式化后的数组

        foreach ($arr as $k => $v) {
            if (is_array($v)) {
                $newArr[$k] = self::changeValueCase($v, $toUpper);
            } else {
                $v          = trim($v);
                $newArr[$k] = $function($v);
            }
        }

        return $newArr;
    }

    /**
     * @param iterable $data
     * @param int $flags
     *
     * @return array
     */
    public static function flattenMap(iterable $data, int $flags = 0): array
    {
        $flatMap = [];
        self::doFlattenMap($data, $flatMap, '', $flags);
        return $flatMap;
    }

    /**
     * @param iterable $data
     * @param array $flatMap
     * @param string $parentPath
     * @param int $flags
     */
    public static function doFlattenMap(iterable $data, array &$flatMap = [], string $parentPath = '', int $flags = 0): void
    {
        foreach ($data as $key => $val) {
            if ($parentPath) {
                if (is_int($key) && $flags^ArrConst::FLAT_DOT_JOIN_INDEX) {
                    $path = $parentPath . "[$key]";
                } else {
                    $path = $parentPath . '.' . $key;
                }
            } else {
                $path = $key;
            }

            if (is_scalar($val) || $val === null) {
                $flatMap[$path] = $val;
            } else {
                self::doFlattenMap($val, $flatMap, $path, $flags);
            }
        }
    }

    /**
     * Quickly build multi line k-v text
     *
     * @param array $data
     * @param string $kvSep
     * @param string $lineSep
     *
     * @return string
     */
    public static function toKVString(array $data, string $kvSep = '=', string $lineSep = "\n"): string
    {
        $lines = [];
        foreach ($data as $key => $val) {
            $lines = $key . $kvSep . DataHelper::toString($val, true);
        }

        return implode($lineSep, $lines);
    }

    /**
     * array 递归 转换成 字符串
     *
     * @param array $array
     * @param int    $length
     * @param int    $cycles 至多循环六次 $num >= 6
     * @param bool   $showKey
     * @param bool   $addMark
     * @param string $separator
     * @param string $string
     *
     * @return string
     */
    public static function toString(
        array $array,
        int $length = 800,
        int $cycles = 6,
        bool $showKey = true,
        bool $addMark = false,
        string $separator = ', ',
        string $string = ''
    ): string {
        if (empty($array)) {
            return '';
        }

        $mark = $addMark ? '\'' : '';
        $num  = 0;

        foreach ($array as $key => $value) {
            $num++;

            if ($num >= $cycles || strlen($string) > $length) {
                $string .= '... ...';
                break;
            }

            $keyStr = $showKey ? $key . '=>' : '';

            if (is_array($value)) {
                $string .= $keyStr . 'Array(' . self::toString(
                        $value,
                        $length,
                        $cycles,
                        $showKey,
                        $addMark,
                        $separator,
                        $string
                    ) . ')' . $separator;
            } elseif (is_object($value)) {
                $string .= $keyStr . 'Object(' . get_class($value) . ')' . $separator;
            } elseif (is_resource($value)) {
                $string .= $keyStr . 'Resource(' . get_resource_type($value) . ')' . $separator;
            } else {
                $value  = strlen($value) > 150 ? substr($value, 0, 150) : $value;
                $string .= $mark . $keyStr . trim(htmlspecialchars($value)) . $mark . $separator;
            }
        }

        return trim($string, $separator);
    }

    /**
     * @param array $array
     * @param int $length
     * @param int $cycles
     * @param bool $showKey
     * @param bool $addMark
     * @param string $separator
     *
     * @return string
     */
    public static function toStringNoKey(
        array $array,
        int $length = 800,
        int $cycles = 6,
        bool $showKey = false,
        bool $addMark = true,
        string $separator = ', '
    ): string {
        return static::toString($array, $length, $cycles, $showKey, $addMark, $separator);
    }

    /**
     * @param array $array
     * @param int   $length
     *
     * @return string
     */
    public static function toFormatString(array $array, int $length = 400): string
    {
        $string = var_export($array, true);

        # 将非空格替换为一个空格
        $string = preg_replace('/[\n\r\t]/', ' ', $string);
        # 去掉两个空格以上的
        $string = preg_replace('/\s(?=\s)/', '', $string);
        $string = trim($string);

        if (strlen($string) > $length) {
            $string = substr($string, 0, $length) . '...';
        }

        return $string;
    }

    /**
     * @param array $array
     *
     * @return array
     */
    public static function toLimitOut(array $array): array
    {
        // static $num = 1;

        foreach ($array as $key => $value) {
            // if ( $num >= $cycles) {
            //     break;
            // }

            if (is_array($value) || is_object($value)) {
                $value = gettype($value) . '(...)';
            } elseif (is_string($value) || is_numeric($value)) {
                $value = strlen(trim($value));
            } else {
                $value = gettype($value) . "($value)";
            }

            $array[$key] = $value;
        }

        // $num++;
        return $array;
    }

    /**
     * @param iterable $data
     *
     * @return string
     */
    public static function toStringV2(iterable $data): string
    {
        $strings = [];

        if (is_array($data) && self::isList($data)) {
            foreach ($data as $value) {
                $strings[] = DataHelper::toString($value, true);
            }
            return '[' . implode(', ', $strings) . ']';
        }

        foreach ($data as $key => $value) {
            $strings[] = sprintf('%s: %s', $key, DataHelper::toString($value, true));
        }
        return '{' . implode(', ', $strings) . '}';
    }
}