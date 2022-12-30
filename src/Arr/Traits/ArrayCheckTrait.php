<?php declare(strict_types=1);

namespace Toolkit\Stdlib\Arr\Traits;

use ArrayAccess;
use Traversable;
use function array_change_key_case;
use function array_diff;
use function array_intersect;
use function array_key_exists;
use function array_keys;
use function explode;
use function in_array;
use function is_array;
use function is_string;
use function strtolower;
use function trim;

/**
 * trait ArrayCheckTrait
 *
 * @author inhere
 * @date 2022/12/30
 */
trait ArrayCheckTrait
{

    /**
     * Determine whether the given value is array accessible.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public static function accessible(mixed $value): bool
    {
        return is_array($value) || $value instanceof ArrayAccess;
    }

    /**
     * Determines if an array is associative(no list).
     * An array is "associative" if it doesn't have sequential numerical keys beginning with zero.
     *
     * @param array $array
     *
     * @return bool
     */
    public static function isAssoc(array $array): bool
    {
        $keys = array_keys($array);

        return array_keys($keys) !== $keys;
    }

    /**
     * check is a list array
     *
     * @param array $arr
     *
     * @return bool
     */
    public static function isList(array $arr): bool
    {
        $keys = array_keys($arr);

        return array_keys($keys) === $keys;
    }

    /**
     * 不区分大小写检测数据键名是否存在
     *
     * @param int|string $key
     * @param array $arr
     *
     * @return bool
     */
    public static function keyExists(int|string $key, array $arr): bool
    {
        return array_key_exists(strtolower($key), array_change_key_case($arr));
    }

    /**
     * ******* 检查 一个或多个值是否全部存在数组中 *******
     * 有一个不存在即返回 false
     *
     * @param array|string $check
     * @param array $sampleArr 只能检查一维数组
     *                                注： 不分类型， 区分大小写  2 == '2' ‘a' != 'A'
     *
     * @return bool
     */
    public static function valueExistsAll(array|string $check, array $sampleArr): bool
    {
        // 以逗号分隔的会被拆开，组成数组
        if (is_string($check)) {
            $check = trim($check, ', ');
            $check = str_contains($check, ',') ? explode(',', $check) : [$check];
        }

        return !array_diff((array)$check, $sampleArr);
    }

    /**
     * ******* 检查 一个或多个值是否存在数组中 *******
     * 有一个存在就返回 true 都不存在 return false
     *
     * @param array|string $check
     * @param array $sampleArr 只能检查一维数组
     *
     * @return bool
     */
    public static function valueExistsOne(array|string $check, array $sampleArr): bool
    {
        // 以逗号分隔的会被拆开，组成数组
        if (is_string($check)) {
            $check = trim($check, ', ');
            $check = str_contains($check, ',') ? explode(',', $check) : [$check];
        }

        return (bool)array_intersect((array)$check, $sampleArr);
    }

    /**
     * ******* 不区分大小写，检查 一个或多个值是否 全存在数组中 *******
     * 有一个不存在即返回 false
     *
     * @param array|string $need
     * @param iterable $arr 只能检查一维数组
     * @param bool $type 是否同时验证类型
     *
     * @return bool|string 不存在的会返回 检查到的 字段，判断时 请使用 ArrHelper::existsAll($need,$arr)===true 来验证是否全存在
     */
    public static function existsAll(array|string $need, iterable $arr, bool $type = false): bool|string
    {
        if (is_array($need)) {
            foreach ($need as $v) {
                self::existsAll($v, $arr, $type);
            }
        } elseif (str_contains($need, ',')) {
            $need = explode(',', $need);
            self::existsAll($need, $arr, $type);
        } else {
            $arr  = self::valueToLower($arr);//小写
            $need = strtolower(trim($need));//小写

            if (!in_array($need, $arr, $type)) {
                return $need;
            }
        }

        return true;
    }

    /**
     * ******* 不区分大小写，检查 一个或多个值是否存在数组中 *******
     * 有一个存在就返回 true 都不存在 return false
     *
     * @param array|string $need
     * @param Traversable|array $arr 只能检查一维数组
     * @param bool $type 是否同时验证类型
     *
     * @return bool
     */
    public static function existsOne(array|string $need, Traversable|array $arr, bool $type = false): bool
    {
        if (is_array($need)) {
            foreach ($need as $v) {
                $result = self::existsOne($v, $arr, $type);
                if ($result) {
                    return true;
                }
            }
        } else {
            if (str_contains($need, ',')) {
                $need = explode(',', $need);

                return self::existsOne($need, $arr, $type);
            }

            $arr  = self::changeValueCase($arr);//小写
            $need = strtolower($need);//小写

            if (in_array($need, $arr, $type)) {
                return true;
            }
        }

        return false;
    }

}