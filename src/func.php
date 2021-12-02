<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

if (!function_exists('vdump')) {
    /**
     * Dump data like var_dump
     *
     * @param mixed ...$vars
     */
    function vdump(...$vars): void
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);

        $line = $trace[0]['line'];
        $pos  = $trace[1]['class'] ?? $trace[0]['file'];

        if ($pos) {
            echo "CALL ON $pos($line):\n";
        }

        echo Toolkit\Stdlib\Php::dumpVars(...$vars), PHP_EOL;
    }
}

if (!function_exists('edump')) {
    /**
     * Dump data like var_dump, will call exit() on print after.
     *
     * @param mixed ...$vars
     */
    function edump(...$vars): void
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);

        $line = $trace[0]['line'];
        $pos  = $trace[1]['class'] ?? $trace[0]['file'];

        if ($pos) {
            echo "CALL ON $pos($line):\n";
        }

        echo Toolkit\Stdlib\Php::dumpVars(...$vars), PHP_EOL;
        exit(0);
    }
}

if (!function_exists('ddump')) {
    /**
     * Dump data like var_dump, will call die() on print after.
     *
     * @param mixed ...$vars
     */
    function ddump(...$vars): void
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);

        $line = $trace[0]['line'];
        $pos  = $trace[1]['class'] ?? $trace[0]['file'];

        if ($pos) {
            echo "CALL ON $pos($line):\n";
        }

        echo Toolkit\Stdlib\Php::dumpVars(...$vars), PHP_EOL;
        die(0);
    }
}

if (!function_exists('pprints')) {
    /**
     * Print data use print_r()
     *
     * @param mixed ...$vars
     */
    function pprint(...$vars): void
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);

        $line = $trace[0]['line'];
        $pos  = $trace[1]['class'] ?? $trace[0]['file'];

        if ($pos) {
            echo "CALL ON $pos($line):\n";
        }

        echo Toolkit\Stdlib\Php::printVars(...$vars), PHP_EOL;
    }
}

if (!function_exists('eprints')) {
    /**
     * Print data use export_var()
     *
     * @param mixed ...$vars
     */
    function eprints(...$vars): void
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);

        $line = $trace[0]['line'];
        $pos  = $trace[1]['class'] ?? $trace[0]['file'];

        if ($pos) {
            echo "CALL ON $pos($line):\n";
        }

        echo Toolkit\Stdlib\Php::exportVar(...$vars), PHP_EOL;
    }
}

if (!function_exists('env_val')) {
    /**
     * @param string $key
     * @param string $default
     *
     * @return string
     */
    function env_val(string $key, string $default = ''): string
    {
        return Toolkit\Stdlib\OS::getEnvStrVal($key, $default);
    }
}