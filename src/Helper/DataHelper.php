<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Helper;

use Toolkit\Stdlib\Json;
use function filter_var;
use function gettype;
use function is_array;
use function is_bool;
use function is_object;
use function is_scalar;
use function is_string;
use function method_exists;
use const FILTER_NULL_ON_FAILURE;
use const FILTER_VALIDATE_BOOLEAN;

/**
 * Class DataHelper
 *
 * @package Toolkit\Stdlib\Helper
 */
class DataHelper
{
    /**
     * 布尔值验证，转换成字符串后是下列的一个，就认为他是个bool值
     *   - "1"、"true"、"on" 和 "yes" (equal TRUE)
     *   - "0"、"false"、"off"、"no" 和 ""(equal FALSE)
     * 注意： NULL 不是标量类型
     *
     * @param int|string $val
     * @param bool  $nullAsFalse
     *
     * @return bool
     */
    public static function boolean(int|string $val, bool $nullAsFalse = false): bool
    {
        return (bool)filter_var($val, FILTER_VALIDATE_BOOLEAN, [
            'flags' => $nullAsFalse ? FILTER_NULL_ON_FAILURE : 0
        ]);
    }

    /**
     * @param mixed $val
     * @param false|mixed $nullAsFalse
     *
     * @return bool
     */
    public static function toBool(mixed $val, bool $nullAsFalse = false): bool
    {
        return self::boolean($val, $nullAsFalse);
    }

    /**
     * @param mixed $val
     * @param bool $simpleBool
     *
     * @return string
     */
    public static function toString(mixed $val, bool $simpleBool = false): string
    {
        if (is_scalar($val)) {
            if (is_bool($val)) {
                if ($simpleBool) {
                    return $val ? 'TRUE' : 'FALSE';
                }
                return $val ? 'bool(TRUE)' : 'bool(FALSE)';
            }

            return is_string($val) ? $val : (string)$val;
        }

        // TIP: null is not scalar type.
        if ($val === null) {
            return 'NULL';
        }

        if (is_array($val)) {
            return Json::enc($val);
        }

        if (is_object($val)) {
            if (method_exists($val, '__toString')) {
                return (string)$val;
            }

            return PhpHelper::dumpVars($val);
        }

        $typeName = gettype($val);
        return '<' . $typeName . '>';
    }
}
