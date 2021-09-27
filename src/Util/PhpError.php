<?php declare(strict_types=1);
/**
 * This file is part of toolkit/sys-utils.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/sys-utils
 * @license  MIT
 */

namespace Toolkit\Stdlib\Util;

use function error_get_last;
use const E_COMPILE_ERROR;
use const E_COMPILE_WARNING;
use const E_CORE_ERROR;
use const E_CORE_WARNING;
use const E_DEPRECATED;
use const E_ERROR;
use const E_NOTICE;
use const E_PARSE;
use const E_RECOVERABLE_ERROR;
use const E_STRICT;
use const E_USER_DEPRECATED;
use const E_USER_ERROR;
use const E_USER_NOTICE;
use const E_USER_WARNING;
use const E_WARNING;

/**
 * Class PhpError
 *
 * @package Toolkit\Stdlib\Util
 */
class PhpError
{
    /**
     * @var array
     */
    public static $fatalErrors = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR];

    /**
     * @return array
     */
    public static function lastError2array(): array
    {
        return self::toArray(error_get_last());
    }

    /**
     * $lastError = error_get_last();
     *
     * @param array       $lastError
     * @param null|string $catcher
     *
     * @return array
     */
    public static function toArray(array $lastError, string $catcher = null): array
    {
        $digest = 'Fatal Error (' . self::codeToString($lastError['type']) . '): ' . $lastError['message'];
        $data   = [
            'code'    => $lastError['type'],
            'message' => $lastError['message'],
            'file'    => $lastError['file'],
            'line'    => $lastError['line'],
            'catcher' => __METHOD__,
        ];

        if ($catcher) {
            $data['catcher'] = $catcher;
        }

        return [$digest, $data];
    }

    /**
     * @param int $code
     *
     * @return string
     */
    public static function codeToString(int $code): string
    {
        switch ($code) {
            case E_ERROR:
                return 'E_ERROR';
            case E_WARNING:
                return 'E_WARNING';
            case E_PARSE:
                return 'E_PARSE';
            case E_NOTICE:
                return 'E_NOTICE';
            case E_CORE_ERROR:
                return 'E_CORE_ERROR';
            case E_CORE_WARNING:
                return 'E_CORE_WARNING';
            case E_COMPILE_ERROR:
                return 'E_COMPILE_ERROR';
            case E_COMPILE_WARNING:
                return 'E_COMPILE_WARNING';
            case E_USER_ERROR:
                return 'E_USER_ERROR';
            case E_USER_WARNING:
                return 'E_USER_WARNING';
            case E_USER_NOTICE:
                return 'E_USER_NOTICE';
            case E_STRICT:
                return 'E_STRICT';
            case E_RECOVERABLE_ERROR:
                return 'E_RECOVERABLE_ERROR';
            case E_DEPRECATED:
                return 'E_DEPRECATED';
            case E_USER_DEPRECATED:
                return 'E_USER_DEPRECATED';
        }

        return 'Unknown PHP error';
    }
}
