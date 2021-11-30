<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Str\Traits;

use function array_merge;
use function array_pop;
use function array_search;
use function array_slice;
use function array_splice;
use function array_unshift;
use function count;
use function function_exists;
use function implode;
use function in_array;
use function preg_match;
use function preg_match_all;
use function preg_replace;
use function strip_tags;
use function strlen;
use function substr;
use function utf8_decode;
use function utf8_encode;
use const PREG_OFFSET_CAPTURE;
use const PREG_SET_ORDER;

/**
 * Trait StringTruncateHelperTrait
 * @package Toolkit\Stdlib\Str\Traits
 */
trait StringTruncateHelperTrait
{

    ////////////////////////////////////////////////////////////////////////
    /// Truncate
    ////////////////////////////////////////////////////////////////////////

    /**
     * @param string   $str
     * @param int      $start
     * @param int|null $length
     * @param string   $encoding
     *
     * @return bool|string
     */
    public static function substr(string $str, int $start, int $length = null, string $encoding = 'utf-8'): bool|string
    {
        if (function_exists('mb_substr')) {
            return mb_substr($str, $start, $length ?? self::strlen($str), $encoding);
        }

        return substr($str, $start, $length ?? self::strlen($str));
    }

    /**
     * @from web
     *  utf-8编码下截取中文字符串,参数可以参照substr函数
     *
     * @param string   $str    要进行截取的字符串
     * @param int      $start  要进行截取的开始位置，负数为反向截取
     * @param int|null $length 要进行截取的长度
     *
     * @return string
     */
    public static function utf8SubStr(string $str, int $start = 0, int $length = null): string
    {
        if (empty($str)) {
            return '';
        }

        if (function_exists('mb_substr')) {
            if ($length) {
                return mb_substr($str, $start, $length, 'utf-8');
            }

            mb_internal_encoding('UTF-8');
            return mb_substr($str, $start);
        }

        $null = '';
        preg_match_all('/./u', $str, $ar);

        if ($length) {
            // $end = func_get_arg(2);
            return implode($null, array_slice($ar[0], $start, $length));
        }

        return implode($null, array_slice($ar[0], $start));
    }

    /**
     * @from web
     * 中文截取，支持gb2312,gbk,utf-8,big5   *
     *
     * @param string $str     要截取的字串
     * @param int    $start   截取起始位置
     * @param int    $length  截取长度
     * @param string $charset utf-8|gb2312|gbk|big5 编码
     * @param bool   $suffix  是否加尾缀
     *
     * @return string
     */
    public static function zhSubStr(string $str, int $start = 0, int $length = 0, string $charset = 'utf-8', bool $suffix = true): string
    {
        if (function_exists('mb_substr')) {
            if (mb_strlen($str, $charset) <= $length) {
                return $str;
            }

            $slice = mb_substr($str, $start, $length, $charset);
        } else {
            $re['utf-8']  = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
            $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
            $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
            $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";

            preg_match_all($re[$charset], $str, $match);
            if (count($match[0]) <= $length) {
                return $str;
            }

            $slice = implode('', array_slice($match[0], $start, $length));
        }

        return $suffix ? $slice . '…' : $slice;
    }

    /**
     * Truncate strings
     *
     * @param string $str
     * @param int    $maxLength Max length
     * @param string $suffix    Suffix optional
     *
     * @return string $str truncated
     */
    /* CAUTION : Use it only on module hookEvents.
    ** For other purposes use the smarty function instead */
    public static function truncate(string $str, int $maxLength, string $suffix = '...'): string
    {
        if (self::strlen($str) <= $maxLength) {
            return $str;
        }

        $str = utf8_decode($str);

        return utf8_encode(substr($str, 0, $maxLength - self::strlen($suffix)) . $suffix);
    }

    /**
     * 字符截断输出
     *
     * @param string   $str
     * @param int      $start
     * @param null|int $length
     *
     * @return string
     */
    public static function truncate2(string $str, int $start, int $length = null): string
    {
        if (!$length) {
            $length = $start;
            $start  = 0;
        }

        if (strlen($str) <= $length) {
            return $str;
        }

        if (function_exists('mb_substr')) {
            $str = mb_substr(strip_tags($str), $start, $length, 'utf-8');
        } else {
            $str = substr($str, $start, $length) . '...';
        }

        return $str;
    }

    /**
     * Copied from CakePHP String utility file
     *
     * @param string $text
     * @param int    $length
     * @param array  $options
     *
     * @return string
     */
    public static function truncate3(string $text, int $length = 120, array $options = []): string
    {
        $default = [
            'ellipsis' => '...',
            'exact'    => true,
            'html'     => true
        ];

        $options  = array_merge($default, $options);
        $ellipsis = $options['ellipsis'];
        $exact    = $options['exact'];
        $html     = $options['html'];

        /**
         * @var string $ellipsis
         * @var bool   $exact
         * @var bool   $html
         */
        if ($html) {
            if (self::strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
                return $text;
            }

            $total_length = self::strlen(strip_tags($ellipsis));
            $open_tags    = $tags = [];
            $truncate     = '';
            preg_match_all('/(<\/?([\w+]+)[^>]*>)?([^<>]*)/', $text, $tags, PREG_SET_ORDER);

            foreach ($tags as $tag) {
                if (!preg_match('/img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param/', $tag[2])) {
                    if (preg_match('/<[\w]+[^>]*>/', $tag[0])) {
                        array_unshift($open_tags, $tag[2]);
                    } elseif (preg_match('/<\/([\w]+)[^>]*>/', $tag[0], $close_tag)) {
                        $pos = array_search($close_tag[1], $open_tags, true);
                        if ($pos !== false) {
                            array_splice($open_tags, $pos, 1);
                        }
                    }
                }
                $truncate       .= $tag[1];
                $content_length = self::strlen(preg_replace(
                    '/&[0-9a-z]{2,8};|&#[\d]{1,7};|&#x[0-9a-f]{1,6};/i',
                    ' ',
                    $tag[3]
                ));

                if ($content_length + $total_length > $length) {
                    $left            = $length - $total_length;
                    $entities_length = 0;

                    if (preg_match_all(
                        '/&[0-9a-z]{2,8};|&#[\d]{1,7};|&#x[0-9a-f]{1,6};/i',
                        $tag[3],
                        $entities,
                        PREG_OFFSET_CAPTURE
                    )
                    ) {
                        foreach ((array)$entities[0] as $entity) {
                            if ($entity[1] + 1 - $entities_length <= $left) {
                                $left--;
                                $entities_length += self::strlen($entity[0]);
                            } else {
                                break;
                            }
                        }
                    }

                    $truncate .= self::substr($tag[3], 0, $left + $entities_length);
                    break;
                }

                $truncate     .= $tag[3];
                $total_length += $content_length;

                if ($total_length >= $length) {
                    break;
                }
            }
        } else {
            if (self::strlen($text) <= $length) {
                return $text;
            }

            $truncate = self::substr($text, 0, $length - self::strlen($ellipsis));
        }

        $open_tags = null;

        if (!$exact) {
            $spacepos = self::strrpos($truncate, ' ');
            if ($html) {
                $truncate_check = self::substr($truncate, 0, $spacepos);
                $last_open_tag  = self::strrpos($truncate_check, '<');
                $last_close_tag = self::strrpos($truncate_check, '>');

                if ($last_open_tag > $last_close_tag) {
                    preg_match_all('/<[\w]+[^>]*>/', $truncate, $last_tag_matches);
                    $last_tag = array_pop($last_tag_matches[0]);
                    $spacepos = self::strrpos($truncate, $last_tag) + self::strlen($last_tag);
                }

                $bits = self::substr($truncate, $spacepos);
                preg_match_all('/<\/([a-z]+)>/', $bits, $dropped_tags, PREG_SET_ORDER);

                /** @var array $dropped_tags */
                if (!empty($dropped_tags)) {
                    if (!empty($open_tags)) {
                        foreach ($dropped_tags as $closing_tag) {
                            if (!in_array($closing_tag[1], $open_tags, true)) {
                                array_unshift($open_tags, $closing_tag[1]);
                            }
                        }
                    } else {
                        foreach ($dropped_tags as $closing_tag) {
                            $open_tags[] = $closing_tag[1];
                        }
                    }
                }
            }

            $truncate = self::substr($truncate, 0, $spacepos);
        }

        $truncate .= $ellipsis;

        if ($html && $open_tags) {
            foreach ((array)$open_tags as $tag) {
                $truncate .= '</' . $tag . '>';
            }
        }

        return $truncate;
    }
}
