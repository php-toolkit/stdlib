<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib;

use function defined;
use function explode;
use function function_exists;
use function getenv;
use function getmyuid;
use function in_array;
use function php_uname;
use function posix_getuid;
use function putenv;
use function rtrim;
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
        return stripos(self::name(), 'Windows') !== false;
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

    /** @var string|null */
    private static $homeDir;

    /**
     * @param bool $refresh
     *
     * @return string
     */
    public static function useHomeDir(bool $refresh = false): string
    {
        return self::getUserHomeDir($refresh);
    }

    /**
     * @param bool $refresh
     *
     * @return string
     */
    public static function getUserHomeDir(bool $refresh = false): string
    {
        // has cache value.
        if (self::$homeDir && $refresh === false) {
            return self::$homeDir;
        }

        if (!$home = self::getEnvVal('HOME')) {
            $isWin = self::isWindows();

            // home on windows
            if ($isWin && !empty($_SERVER['HOMEDRIVE']) && !empty($_SERVER['HOMEPATH'])) {
                $home = $_SERVER['HOMEDRIVE'] . $_SERVER['HOMEPATH'];
                // If HOMEPATH is a root directory the path can end with a slash.
                // Make sure that doesn't happen.
                $home = rtrim($home, '\\/');
            }
        }

        self::$homeDir = $home;
        return $home;
    }

    /**
     * @param string $key
     * @param string $default
     *
     * @return string
     */
    public static function getEnvVal(string $key, string $default = ''): string
    {
        return getenv($key) ?: (string)($_SERVER[$key] ?? $default);
    }

    /**
     * @param string $key
     * @param string|int $value
     *
     * @return bool
     */
    public static function setEnvVar(string $key, $value): bool
    {
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
        return putenv($key . '=' . $value);
    }

    /**
     * @param array $kvMap
     *
     * @return void
     */
    public static function setEnvVars(array $kvMap): void
    {
        foreach ($kvMap as $key => $value) {
            self::setEnvVar($key, $value);
        }
    }

    /**
     * @return array
     */
    public static function getEnvPaths(): array
    {
        $pathStr = $_SERVER['PATH'] ?? '';
        if (!$pathStr) {
            return [];
        }

        $sepChar = self::isWindows() ? ';' : ':';
        return explode($sepChar, $pathStr);
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
