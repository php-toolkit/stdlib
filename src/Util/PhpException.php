<?php declare(strict_types=1);
/**
 * This file is part of toolkit/sys-utils.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/sys-utils
 * @license  MIT
 */

namespace Toolkit\Stdlib\Util;

use Throwable;
use function get_class;
use function json_encode;
use function strip_tags;

/**
 * Class PhpException
 *
 * @package Toolkit\Stdlib\Util
 */
class PhpException
{
    /**
     * @param Throwable $e
     * @param bool      $getTrace
     * @param string|null      $catcher
     *
     * @return string
     * @see PhpException::toHtml()
     */
    public static function toText(Throwable $e, bool $getTrace = true, string $catcher = null): string
    {
        return self::toHtml($e, $getTrace, $catcher, true);
    }

    /**
     * @param Throwable $e
     * @param bool      $getTrace
     * @param string|null      $catcher
     *
     * @return string
     * @see PhpException::toHtml()
     */
    public static function toString(Throwable $e, bool $getTrace = true, string $catcher = null): string
    {
        return self::toHtml($e, $getTrace, $catcher, true);
    }

    /**
     * Converts an exception into a simple string.
     *
     * @param Throwable   $e the exception being converted
     * @param bool        $clearHtml
     * @param bool        $getTrace
     * @param null|string $catcher
     *
     * @return string the string representation of the exception.
     */
    public static function toHtml(Throwable $e, bool $getTrace = true, string $catcher = null, bool $clearHtml = false): string
    {
        if (!$getTrace) {
            $message = "Error: {$e->getMessage()}";
        } else {
            $message = sprintf(
                "<h3>%s(%d): %s</h3>\n<pre><strong>File: %s(Line %d)</strong>%s \n\n%s</pre>",
                get_class($e),
                $e->getCode(),
                $e->getMessage(),
                $e->getFile(),
                $e->getLine(),
                $catcher ? "\nCatch By: $catcher" : '',
                $e->getTraceAsString()
            );
        }

        return $clearHtml ? strip_tags($message) : "<div class=\"exception-box\">$message</div>";
    }

    /**
     * Converts an exception into a simple array.
     *
     * @param Throwable   $e the exception being converted
     * @param bool        $getTrace
     * @param null|string $catcher
     *
     * @return array
     */
    public static function toArray(Throwable $e, bool $getTrace = true, string $catcher = null): array
    {
        $data = [
            'class'   => get_class($e),
            'message' => $e->getMessage(),
            'code'    => $e->getCode(),
            'file'    => $e->getFile() . ':' . $e->getLine(),
        ];

        if ($catcher) {
            $data['catcher'] = $catcher;
        }

        if ($getTrace) {
            $data['trace'] = $e->getTrace();
        }

        return $data;
    }

    /**
     * Converts an exception into a json string.
     *
     * @param Throwable   $e the exception being converted
     * @param bool        $getTrace
     * @param null|string $catcher
     *
     * @return string the string representation of the exception.
     */
    public static function toJson(Throwable $e, bool $getTrace = true, string $catcher = null): string
    {
        $errMsg = $e->getMessage();
        if (!$getTrace) {
            return json_encode(['msg' => "Error: $errMsg"]);
        }

        $map = [
            'code' => $e->getCode() ?: 500,
            'msg'  => sprintf(
                '%s(%d): %s, File: %s(Line %d)',
                get_class($e),
                $e->getCode(),
                $errMsg,
                $e->getFile(),
                $e->getLine()
            ),
            'data' => $e->getTrace()
        ];

        if ($catcher) {
            $map['catcher'] = $catcher;
        }

        $map['trace'] = $e->getTrace();
        return json_encode($map);
    }
}
