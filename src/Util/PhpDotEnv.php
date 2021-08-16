<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Util;

use function is_file;
use function is_readable;
use function parse_ini_file;
use function array_flip;
use function explode;
use function getenv;
use function is_int;
use function is_string;
use function strtoupper;
use function strpos;
use function defined;
use function constant;
use function implode;
use function array_keys;
use function putenv;

/**
 * Class PhpDotEnv - local env read
 *
 * @package Toolkit\Stdlib\Util
 *
 * in local config file `.env` (must is 'ini' format):
 *
 * ```ini
 * APP_ENV=dev
 * DEBUG=true
 * ... ...
 * ```
 *
 * IN CODE:
 *
 * ```php
 * PhpDotEnv::load(__DIR__);
 * env('DEBUG', false);
 * env('APP_ENV', 'pdt');
 * ```
 */
class PhpDotEnv
{
    public const FULL_KEY = 'PHP_DOTENV_VARS';

    /**
     * @param string $fileDir
     * @param string $fileName
     *
     * @return static
     */
    public static function load(string $fileDir, string $fileName = '.env'): self
    {
        return new self($fileDir, $fileName);
    }

    /**
     * constructor.
     *
     * @param string $fileDir
     * @param string $fileName
     */
    public function __construct(string $fileDir, string $fileName = '.env')
    {
        $file = $fileDir . DIRECTORY_SEPARATOR . ($fileName ?: '.env');

        $this->add($file);
    }

    /**
     * @param string $file
     */
    public function add(string $file): void
    {
        if (is_file($file) && is_readable($file)) {
            $this->settingEnv(parse_ini_file($file));
        }
    }

    /**
     * setting env data
     *
     * @param array $data
     */
    private function settingEnv(array $data): void
    {
        $loadedVars = array_flip(explode(',', getenv(self::FULL_KEY)));
        unset($loadedVars['']);

        foreach ($data as $name => $value) {
            if (is_int($name) || !is_string($value)) {
                continue;
            }

            $name        = strtoupper($name);
            $notHttpName = 0 !== strpos($name, 'HTTP_');

            // don't check existence with getenv() because of thread safety issues
            if ((isset($_ENV[$name]) || (isset($_SERVER[$name]) && $notHttpName)) && !isset($loadedVars[$name])) {
                continue;
            }

            // is a constant var
            if ($value && defined($value)) {
                $value = constant($value);
            }

            // eg: "FOO=BAR"
            putenv("$name=$value");
            $_ENV[$name] = $value;

            if ($notHttpName) {
                $_SERVER[$name] = $value;
            }

            $loadedVars[$name] = true;
        }

        if ($loadedVars) {
            $loadedVars = implode(',', array_keys($loadedVars));
            putenv(self::FULL_KEY . "=$loadedVars");
            $_ENV[self::FULL_KEY]    = $loadedVars;
            $_SERVER[self::FULL_KEY] = $loadedVars;
        }
    }
}
