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
use function defined;
use function constant;
use function implode;
use function array_keys;
use function putenv;

/**
 * Class PhpDotEnv - local env read
 *
 * The env config file `.env` (must is 'ini' format):
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
 *
 * @package Toolkit\Stdlib\Util
 */
class PhpDotEnv
{
    public const FULL_ENV_KEY = 'PHP_DOTENV_VARS';

    public const DEFAULT_NAME = '.env';

    /**
     * @var self|null
     */
    private static ?self $global = null;

    /**
     * @var array
     */
    private array $loadedFiles = [];

    /**
     * @return static
     */
    public static function global(): self
    {
        if (!self::$global) {
            self::$global = new self('');
        }

        return self::$global;
    }

    /**
     * @param string $fileDir
     * @param string $fileName
     *
     * @return static
     */
    public static function load(string $fileDir, string $fileName = self::DEFAULT_NAME): self
    {
        return new self($fileDir, $fileName);
    }

    /**
     * class constructor.
     *
     * @param string $fileDir
     * @param string $fileName
     */
    public function __construct(string $fileDir, string $fileName = self::DEFAULT_NAME)
    {
        if ($fileDir) {
            $file = $fileDir . DIRECTORY_SEPARATOR . ($fileName ?: self::DEFAULT_NAME);
            $this->add($file);
        }
    }

    /**
     * @param string $file
     */
    public function add(string $file): void
    {
        if (is_file($file) && is_readable($file)) {
            $this->loadedFiles[] = $file;
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
        $loadedVars = array_flip(explode(',', (string)getenv(self::FULL_ENV_KEY)));
        unset($loadedVars['']);

        foreach ($data as $name => $value) {
            if (is_int($name) || !is_string($value)) {
                continue;
            }

            // fix: comments start with #
            if ($name[0] === '#') {
                continue;
            }

            $name = strtoupper($name);

            // don't check existence with getenv() because of thread safety issues
            $notHttpName = !str_starts_with($name, 'HTTP_');
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
            putenv(self::FULL_ENV_KEY . "=$loadedVars");
            $_ENV[self::FULL_ENV_KEY]    = $loadedVars;
            $_SERVER[self::FULL_ENV_KEY] = $loadedVars;
        }
    }

    /**
     * @return array
     */
    public function getLoadedFiles(): array
    {
        return $this->loadedFiles;
    }
}
