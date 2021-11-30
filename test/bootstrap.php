<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

error_reporting(E_ALL | E_STRICT);
date_default_timezone_set('Asia/Shanghai');

spl_autoload_register(static function ($class): void {
    $file = '';

    if (str_starts_with($class, 'Toolkit\Stdlib\Example\\')) {
        $path = str_replace('\\', '/', substr($class, strlen('Toolkit\Stdlib\Example\\')));
        $file = dirname(__DIR__) . "/example/$path.php";
    } elseif (str_starts_with($class, 'Toolkit\StdlibTest\\')) {
        $path = str_replace('\\', '/', substr($class, strlen('Toolkit\StdlibTest\\')));
        $file = __DIR__ . "/$path.php";
    } elseif (str_starts_with($class, 'Toolkit\Stdlib\\')) {
        $path = str_replace('\\', '/', substr($class, strlen('Toolkit\Stdlib\\')));
        $file = dirname(__DIR__) . "/src/$path.php";
    }

    if ($file && is_file($file)) {
        include $file;
    }
});

if (is_file(dirname(__DIR__, 3) . '/autoload.php')) {
    require dirname(__DIR__, 3) . '/autoload.php';
} elseif (is_file(dirname(__DIR__) . '/vendor/autoload.php')) {
    require dirname(__DIR__) . '/vendor/autoload.php';
}
