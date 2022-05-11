<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
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
    public static array $fatalErrors = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR];

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
        return match ($code) {
            E_ERROR => 'E_ERROR',
            E_WARNING => 'E_WARNING',
            E_PARSE => 'E_PARSE',
            E_NOTICE => 'E_NOTICE',
            E_CORE_ERROR => 'E_CORE_ERROR',
            E_CORE_WARNING => 'E_CORE_WARNING',
            E_COMPILE_ERROR => 'E_COMPILE_ERROR',
            E_COMPILE_WARNING => 'E_COMPILE_WARNING',
            E_USER_ERROR => 'E_USER_ERROR',
            E_USER_WARNING => 'E_USER_WARNING',
            E_USER_NOTICE => 'E_USER_NOTICE',
            E_STRICT => 'E_STRICT',
            E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
            E_DEPRECATED => 'E_DEPRECATED',
            E_USER_DEPRECATED => 'E_USER_DEPRECATED',
            default => 'Unknown PHP error',
        };
    }
}
