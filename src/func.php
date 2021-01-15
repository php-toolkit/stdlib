<?php

if (!function_exists('vdump')) {
    /**
     * Dump data like var_dump
     *
     * @param mixed ...$vars
     */
    function vdump(...$vars)
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
    function edump(...$vars)
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
    function ddump(...$vars)
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
