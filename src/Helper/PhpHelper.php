<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Helper;

use Closure;
use Generator;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use RuntimeException;
use Throwable;
use Toolkit\Stdlib\Obj\ObjectHelper;
use Toolkit\Stdlib\Util\PhpError;
use Toolkit\Stdlib\Util\PhpException;
use function array_shift;
use function array_sum;
use function basename;
use function error_get_last;
use function explode;
use function fopen;
use function ftok;
use function function_exists;
use function get_class;
use function is_array;
use function is_callable;
use function is_object;
use function is_string;
use function memory_get_peak_usage;
use function memory_get_usage;
use function method_exists;
use function microtime;
use function number_format;
use function ob_get_clean;
use function ob_start;
use function preg_replace;
use function sprintf;
use function stat;
use function strlen;
use function strtoupper;
use function var_dump;
use function var_export;

/**
 * Class PhpHelper
 *
 * @package Toolkit\PhpKit
 */
class PhpHelper
{
    /**
     * @var ReflectionClass[]
     */
    private static array $reflects = [];

    /**
     * @var ReflectionMethod[]
     */
    private static array $reflectMths = [];

    /**
     * @param object|string $classOrObj
     *
     * @return ReflectionClass
     * @throws ReflectionException
     */
    public static function reflectClass(object|string $classOrObj): ReflectionClass
    {
        $id = is_string($classOrObj) ? $classOrObj : get_class($classOrObj);

        if (!isset(self::$reflects[$id])) {
            self::$reflects[$id] = new ReflectionClass($classOrObj);
        }

        return self::$reflects[$id];
    }

    /**
     * @param object|string $classOrObj
     * @param string $method
     *
     * @return ReflectionMethod
     * @throws ReflectionException
     */
    public static function reflectMethod(object|string $classOrObj, string $method): ReflectionMethod
    {
        $id = is_string($classOrObj) ? $classOrObj : get_class($classOrObj);
        $id .= '.' . $method;

        if (!isset(self::$reflectMths[$id])) {
            self::$reflectMths[$id] = new ReflectionMethod($classOrObj, $method);
        }

        return self::$reflectMths[$id];
    }

    /**
     * @param ReflectionClass $reflectClass
     * @param int             $flags eg: \ReflectionMethod::IS_PUBLIC
     * @param Closure|null   $nameFilter
     *
     * @return Generator
     */
    public static function getReflectMethods(ReflectionClass $reflectClass, int $flags = 0, Closure $nameFilter = null): ?Generator
    {
        foreach ($reflectClass->getMethods($flags) as $m) {
            $mName = $m->getName();

            if ($nameFilter && false === $nameFilter($mName)) {
                continue;
            }

            yield $mName => $m;
        }
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public static function value($value): mixed
    {
        if (is_callable($value)) {
            return $value();
        }

        return $value;
    }

    /**
     * @param string $mode
     *
     * @return resource
     */
    public static function newMemoryStream(string $mode = 'rwb')
    {
        $handle = fopen('php://memory', $mode);

        if (!is_resource($handle)) {
            throw new RuntimeException('create temp memory stream fail');
        }

        return $handle;
    }

    private static ?string $scriptName = null;

    /**
     * @param bool $refresh
     *
     * @return string
     */
    public static function getBinName(bool $refresh = false): string
    {
        if (!$refresh && self::$scriptName !== null) {
            return self::$scriptName;
        }

        $scriptName = '';
        if (isset($_SERVER['argv']) && ($argv = $_SERVER['argv'])) {
            $scriptFile = array_shift($argv);
            $scriptName = basename($scriptFile);
        }

        self::$scriptName = $scriptName;
        return self::$scriptName;
    }

    /**
     * get $_SERVER value
     *
     * @param string $name
     * @param mixed $default
     *
     * @return mixed
     */
    public static function serverParam(string $name, mixed $default = ''): mixed
    {
        $name = strtoupper($name);

        return $_SERVER[$name] ?? $default;
    }

    /**
     * @param callable|mixed $cb
     * @param array          ...$args
     *
     * @return mixed
     */
    public static function call(mixed $cb, ...$args): mixed
    {
        if (is_string($cb)) {
            // function
            if (!str_contains($cb, '::')) {
                return $cb(...$args);
            }

            // ClassName::method
            $cb = explode('::', $cb, 2);
        } elseif (is_object($cb) && method_exists($cb, '__invoke')) {
            return $cb(...$args);
        }

        if (is_array($cb)) {
            [$obj, $mhd] = $cb;

            return is_object($obj) ? $obj->$mhd(...$args) : $obj::$mhd(...$args);
        }

        return $cb(...$args);
    }

    /**
     * @param callable $cb
     * @param array    $args
     *
     * @return mixed
     */
    public static function callByArray(callable $cb, array $args): mixed
    {
        return self::call($cb, ...$args);
    }

    /**
     * Set property values for object
     * - 会先尝试用 setter 方法设置属性
     * - 再尝试直接设置属性
     *
     * @param object $object An object instance
     * @param array $options
     *
     * @return mixed
     */
    public static function initObject(object $object, array $options): object
    {
        return ObjectHelper::init($object, $options);
    }

    /**
     * 获取资源消耗
     *
     * @param int       $startTime
     * @param float|int $startMem
     * @param array     $info
     * @param bool      $realUsage
     *
     * @return array
     */
    public static function runtime(int $startTime, float|int $startMem, array $info = [], bool $realUsage = false): array
    {
        $info['startTime'] = $startTime;
        $info['endTime']   = microtime(true);
        $info['endMemory'] = memory_get_usage($realUsage);

        // 计算运行时间
        $info['runtime'] = number_format(($info['endTime'] - $startTime) * 1000, 3) . 'ms';

        if ($startMem) {
            $startMem = array_sum(explode(' ', (string)$startMem));
            $endMem   = array_sum(explode(' ', (string)$info['endMemory']));

            $info['memory'] = number_format(($endMem - $startMem) / 1024, 3) . 'kb';
        }

        $peakMem = memory_get_peak_usage(true) / 1024 / 1024;
        // record
        $info['peakMemory'] = number_format($peakMem, 3) . 'Mb';

        return $info;
    }

    /**
     * Returns the last occurred PHP error or an empty string if no error occurred.
     *
     * @return string
     */
    public static function getLastError(): string
    {
        $message = error_get_last()['message'] ?? '';
        // $message = ini_get('html_errors') ? Html::htmlToText($message) : $message;

        return preg_replace('#^\w+\(.*?\): #', '', $message);
    }

    /**
     * Usage:
     *
     * $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
     * $position = PhpHelper::formatBacktrace($backtrace, 2);
     *
     * @param array $traces
     * @param int   $index
     *
     * @return string
     */
    public static function formatBacktrace(array $traces, int $index): string
    {
        $position = 'UNKNOWN';

        if (isset($traces[$index+1])) {
            $tInfo = $traces[$index];
            $prev  = $traces[$index+1];

            $position = sprintf('%s.%s:%d', $prev['class'], $prev['function'] ?? 'UNKNOWN', $tInfo['line']);
        }

        return $position;
    }

    /**
     * dump vars
     *
     * @param array ...$args
     *
     * @return string
     */
    public static function dumpVars(...$args): string
    {
        ob_start();
        var_dump(...$args);
        $string = ob_get_clean();

        return preg_replace("/=>\n\s+/", '=> ', $string);
    }

    /**
     * print vars
     *
     * @param array ...$args
     *
     * @return string
     */
    public static function printVars(...$args): string
    {
        $string = '';

        foreach ($args as $arg) {
            $string .= print_r($arg, true) . PHP_EOL;
        }

        return preg_replace("/Array\n\s+\(/", 'Array (', $string);
    }

    /**
     * @param mixed $vars
     *
     * @return string
     */
    public static function exportVar(...$vars): string
    {
        $string = '';
        foreach ($vars as $var) {
            $string .= var_export($var, true). PHP_EOL;
        }

        return preg_replace('/=>\s+\n\s+array \(/', '=> array (', $string);
    }

    /**
     * @return array
     */
    public static function lastError2array(): array
    {
        return PhpError::lastError2array();
    }

    /**
     * @param Throwable $e
     * @param bool $getTrace
     * @param string|null $catcher
     *
     * @return string
     */
    public static function exception2string(Throwable $e, bool $getTrace = true, string $catcher = null): string
    {
        return PhpException::toString($e, $getTrace, $catcher);
    }

    /**
     * @param Throwable $e
     * @param bool $getTrace
     * @param string|null $catcher
     *
     * @return string
     */
    public static function exception2html(Throwable $e, bool $getTrace = true, string $catcher = null): string
    {
        return PhpException::toHtml($e, $getTrace, $catcher);
    }

    /**
     * @param $anyData
     *
     * @return string
     */
    public static function toString($anyData): string
    {
        return DataHelper::toString($anyData);
    }

    /**
     * @param string     $pathname
     * @param int|string $projectId This must be a one character
     *
     * @return int|string
     */
    public static function ftok(string $pathname, int|string $projectId): int|string
    {
        if (strlen($projectId) > 1) {
            throw new RuntimeException("The project id must be a one character(int/str). Input: $projectId");
        }

        if (function_exists('ftok')) {
            return ftok($pathname, $projectId);
        }

        if (!$st = @stat($pathname)) {
            return -1;
        }

        return sprintf('%u', ($st['ino'] & 0xffff) | (($st['dev'] & 0xff) << 16) | (($projectId & 0xff) << 24));
    }
}
