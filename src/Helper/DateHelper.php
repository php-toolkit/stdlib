<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Helper;

use function date;
use function floor;
use function is_numeric;
use function strlen;
use function strtotime;

/**
 * Class DateHelper
 */
class DateHelper
{
    /**
     * 判断给定的 字符串 是否是个 时间戳
     *
     * @param int|string $timestamp 时间戳
     *
     * @return bool
     */
    public static function isTimestamp(int|string $timestamp): bool
    {
        if (!is_numeric($timestamp) || 10 !== strlen($timestamp)) {
            return false;
        }

        return (bool)date('Ymd', $timestamp);
    }

    /**
     * 校验值是否是日期格式
     *
     * @param string $date 日期
     *
     * @return boolean
     */
    public static function isDate(string $date): bool
    {
        return strtotime($date) > 0;
    }

    /**
     * 校验值是否是日期并且是否满足设定格式
     *
     * @param string $date   日期
     * @param string $format 需要检验的格式数组
     *
     * @return boolean
     */
    public static function isDateFormat(string $date, string $format = 'Y-m-d'): bool
    {
        if (!$unixTime = strtotime($date)) {
            return false;
        }

        // 校验日期的格式有效性
        if (date($format, $unixTime) === $date) {
            return true;
        }

        return false;
    }

    /**
     * @return int
     */
    public static function todayStart(): int
    {
        return strtotime('today 00:00:00');
    }

    /**
     * @return int
     */
    public static function todayEnd(): int
    {
        return strtotime('today 23:59:59');
    }

    /**
     * @return false|int
     */
    public static function tomorrowBegin(): bool|int
    {
        return mktime(0, 0, 0, date('m'), date('d') + 1, date('Y'));
    }

    /**
     * @return int
     */
    public static function tomorrowStart(): int
    {
        return strtotime('+1 day 00:00:00');
    }

    /**
     * @return int
     */
    public static function tomorrowEnd(): int
    {
        return strtotime('+1 day 23:59:59');
    }

    /**
     * @return int
     */
    public static function tomorrow(): int
    {
        return strtotime('+1 day');
    }

    /**
     * 获取指定日期所在月的第一天和最后一天
     *
     * @param string $date
     *
     * @return array
     */
    public static function getTheMonth(string $date): array
    {
        $firstDay = date('Y-m-01', strtotime($date));
        $lastDay  = date('Y-m-d', strtotime("$firstDay +1 month -1 day"));

        return [$firstDay, $lastDay];
    }

    /**
     * 获取指定日期上个月的第一天和最后一天
     *
     * @param string $date
     *
     * @return array
     */
    public static function getPurMonth(string $date): array
    {
        $time = strtotime($date);

        $firstDay = date('Y-m-01', strtotime(date('Y', $time) . '-' . (date('m', $time) - 1) . '-01'));
        $lastDay  = date('Y-m-d', strtotime("$firstDay +1 month -1 day"));

        return [$firstDay, $lastDay];
    }

    /**
     * 获取指定日期下个月的第一天和最后一天
     *
     * @param string $date
     *
     * @return array
     */
    public static function getNextMonth(string $date): array
    {
        $arr = getdate();

        if ($arr['mon'] === 12) {
            $year  = $arr['year'] + 1;
            $month = $arr['mon'] - 11;
            $day   = $arr['mday'];

            $mday = $day < 10 ? '0' . $day : $day;

            $firstDay = $year . '-0' . $month . '-01';
            $lastDay  = $year . '-0' . $month . '-' . $mday;
        } else {
            $time     = strtotime($date);
            $firstDay = date('Y-m-01', strtotime(date('Y', $time) . '-' . (date('m', $time) + 1) . '-01'));
            $lastDay  = date('Y-m-d', strtotime("$firstDay +1 month -1 day"));
        }

        return [$firstDay, $lastDay];
    }

    /**
     * 获得几天前，几小时前，几月前
     *
     * @param int   $time
     * @param array $unit
     *
     * @return string
     */
    public static function before(int $time, array $unit = []): ?string
    {
        $unit = $unit ?: ['年', '月', '星期', '日', '小时', '分钟', '秒'];

        $nowTime  = time();
        $diffTime = $nowTime - $time;

        return match (true) {
            $time < ($nowTime - 31536000) => floor($diffTime / 31536000) . $unit[0],
            $time < ($nowTime - 2592000) => floor($diffTime / 2592000) . $unit[1],
            $time < ($nowTime - 604800) => floor($diffTime / 604800) . $unit[2],
            $time < ($nowTime - 86400) => floor($diffTime / 86400) . $unit[3],
            $time < ($nowTime - 3600) => floor($diffTime / 3600) . $unit[4],
            $time < ($nowTime - 60) => floor($diffTime / 60) . $unit[5],
            default => floor($diffTime) . $unit[6],
        };
    }
}
