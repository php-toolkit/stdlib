<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib;

use InvalidArgumentException;
use RuntimeException;
use function defined;
use function explode;
use function file_get_contents;
use function file_put_contents;
use function function_exists;
use function getcwd;
use function getenv;
use function getmyuid;
use function in_array;
use function is_dir;
use function is_file;
use function is_writable;
use function mkdir;
use function php_uname;
use function posix_getpwuid;
use function posix_getuid;
use function putenv;
use function rtrim;
use function stripos;
use function tempnam;
use function tmpfile;
use const PHP_OS;
use const PHP_OS_FAMILY;

/**
 * Class OsEnv
 *
 * @package Toolkit\PhpKit\OS
 */
class OS
{
    /**************************************************************************
     * user info
     *************************************************************************/

    /** @var string|null */
    private static ?string $homeDir = null;

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

        if (!$home = self::getEnvStrVal('HOME')) {
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
     * @param string $path
     * @param bool   $refresh
     *
     * @return string
     */
    public static function userHomeDir(string $path = '', bool $refresh = false): string
    {
        return self::getUserHomeDir($refresh) . ($path ? "/$path" : '');
    }

    /**
     * @param string $path
     *
     * @return string eg: ~/.config/kite.php
     */
    public static function userConfigDir(string $path = ''): string
    {
        return self::getUserHomeDir() . '/.config' . ($path ? "/$path" : '');
    }

    /**
     * @param string $path
     *
     * @return string  eg: ~/.cache/kite/some.log
     */
    public static function userCacheDir(string $path = ''): string
    {
        return self::getUserHomeDir() . '/.cache' . ($path ? "/$path" : '');
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
     * @return string
     */
    public static function getUserName(): string
    {
        $key = self::isWindows() ? 'USERNAME' : 'USER';

        if (isset($_SERVER[$key])) {
            return $_SERVER[$key];
        }

        if (isset($_ENV[$key])) {
            return $_ENV[$key];
        }

        $user = self::getCurrentUser();
        return $user['name'] ?? '';
    }

    /**
     * Get unix user of current process.
     *
     * @return array{name:string, uid: int, gid: int, dir: string, shell: string}
     */
    public static function getCurrentUser(): array
    {
        return (array)posix_getpwuid(posix_getuid());
    }

    /**************************************************************************
     * system env
     *************************************************************************/

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
     * @return string
     */
    public static function getWorkDir(): string
    {
        return (string)getcwd();
    }

    /**
     * Creates a temporary file
     *
     * @return resource
     */
    public static function newTempFile()
    {
        $fh = tmpfile();
        if ($fh === false) {
            throw new RuntimeException('create an temporary file fail');
        }

        return $fh;
    }

    /**
     * @param string $prefix
     *
     * @return string
     */
    public static function tempFilePath(string $prefix = 'tmp_'): string
    {
        return tempnam(self::getTempDir(), $prefix);
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
     * @param string $key
     * @param string|mixed $default
     *
     * @return mixed
     */
    public static function getEnvVal(string $key, string $default = ''): mixed
    {
        return getenv($key) ?: ($_SERVER[$key] ?? $default);
    }

    /**
     * @param string $key
     * @param string $default
     *
     * @return string
     */
    public static function getEnvStrVal(string $key, string $default = ''): string
    {
        return (string)self::getEnvVal($key, $default);
    }

    /**
     * @param string $key
     * @param int|string $value
     *
     * @return bool
     */
    public static function setEnvVar(string $key, int|string $value): bool
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
     * @param int|resource|mixed $fileDescriptor
     *
     * @return boolean
     */
    public static function isInteractive(mixed $fileDescriptor): bool
    {
        return function_exists('posix_isatty') && @posix_isatty($fileDescriptor);
    }

    /**
     * @param string $filepath
     *
     * @return string
     */
    public static function readFile(string $filepath): string
    {
        if (!is_file($filepath)) {
            throw new InvalidArgumentException('no such file: ' . $filepath);
        }

        return file_get_contents($filepath);
    }

    /**
     * @param string $filepath
     * @param string $contents
     * @param int $flags
     *
     * @return int
     */
    public static function writeFile(string $filepath, string $contents, int $flags = 0): int
    {
        // if (!is_dir($dir = dirname($filepath))) {
        //     self::mkdir($dir);
        // }

        return (int)file_put_contents($filepath, $contents, $flags);
    }

    /**
     * Support the creation of hierarchical directories
     *
     * @param string    $path
     * @param int $mode
     * @param bool       $recursive
     *
     * @return bool
     */
    public static function mkdir(string $path, int $mode = 0775, bool $recursive = true): bool
    {
        return (is_dir($path) || !(!@mkdir($path, $mode, $recursive) && !is_dir($path))) && is_writable($path);
    }
}
