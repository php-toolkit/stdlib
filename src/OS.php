<?php declare(strict_types=1);
/**
 * This file is part of toolkit/phpkit.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/phpkit
 * @license  MIT
 */

namespace Toolkit\Stdlib;

use function defined;
use function function_exists;
use function getmyuid;
use function in_array;
use function php_uname;
use function posix_getuid;
use function stripos;
use const PHP_OS;
use const PHP_OS_FAMILY;

/**
 * Class OsEnv
 *
 * @package Toolkit\PhpKit\OS
 */
class OS
{
    /**
     * @return string
     */
    public static function name(): string
    {
        if (defined('PHP_OS_FAMILY')) {
            return PHP_OS_FAMILY;
        }

        return PHP_OS;
    }

    /**************************************************************************
     * system env
     *************************************************************************/

    /**
     * @return bool
     */
    public static function isUnix(): bool
    {
        $uNames = ['CYG', 'DAR', 'FRE', 'HP-', 'IRI', 'LIN', 'NET', 'OPE', 'SUN', 'UNI'];

        return in_array(strtoupper(substr(PHP_OS, 0, 3)), $uNames, true);
    }

    /**
     * @return bool
     */
    public static function isLinux(): bool
    {
        return stripos(self::name(), 'LIN') !== false;
    }

    /**
     * @return bool
     */
    public static function isWin(): bool
    {
        return self::isWindows();
    }

    /**
     * @return bool
     */
    public static function isWindows(): bool
    {
        return stripos(self::name(), 'WIN') !== false;
    }

    /**
     * @return bool
     */
    public static function isMac(): bool
    {
        return self::isMacOS();
    }

    /**
     * @return bool
     */
    public static function isMacOS(): bool
    {
        return stripos(self::name(), 'Darwin') !== false;
    }

    /**
     * @return bool
     */
    public static function isRoot(): bool
    {
        return self::isRootUser();
    }

    /**
     * @return bool
     */
    public static function isRootUser(): bool
    {
        if (function_exists('posix_getuid')) {
            return posix_getuid() === 0;
        }

        return getmyuid() === 0;
    }

    /**
     * Get unix user of current process.
     *
     * @return array
     */
    public static function getCurrentUser(): array
    {
        return posix_getpwuid(posix_getuid());
    }

    /**
     * @return string
     */
    public static function tempDir(): string
    {
        return self::getTempDir();
    }

    /**
     * @return string
     */
    public static function getTempDir(): string
    {
        // @codeCoverageIgnoreStart
        if (function_exists('sys_get_temp_dir')) {
            $tmp = sys_get_temp_dir();
        } elseif (!empty($_SERVER['TMP'])) {
            $tmp = $_SERVER['TMP'];
        } elseif (!empty($_SERVER['TEMP'])) {
            $tmp = $_SERVER['TEMP'];
        } elseif (!empty($_SERVER['TMPDIR'])) {
            $tmp = $_SERVER['TMPDIR'];
        } else {
            $tmp = getcwd();
        }
        // @codeCoverageIgnoreEnd

        return $tmp;
    }

    /**
     * @return string
     */
    public static function getHostname(): string
    {
        return php_uname('n');
    }

    /**
     * @return string
     */
    public static function getNullDevice(): string
    {
        if (self::isUnix()) {
            return '/dev/null';
        }

        return 'NUL';
    }

    /**
     * Returns if the file descriptor is an interactive terminal or not.
     *
     * @param int|resource $fileDescriptor
     *
     * @return boolean
     */
    public static function isInteractive($fileDescriptor): bool
    {
        return function_exists('posix_isatty') && @posix_isatty($fileDescriptor);
    }
}
