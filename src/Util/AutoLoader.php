<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Util;

use InvalidArgumentException;
use function array_merge;
use function file_exists;
use function spl_autoload_register;
use function spl_autoload_unregister;
use function str_replace;
use function strlen;
use function strpos;
use function substr;
use const DIRECTORY_SEPARATOR;

/**
 * Class AutoLoader - an simple class loader
 *
 * @package Toolkit\Stdlib\Util
 *
 * ```php
 * AutoLoader::addFiles([
 *  // file
 * ]);
 * $loader = AutoLoader::getLoader();
 * $loader->addPsr4Map([
 *  'namespace' => 'path'
 * ]);
 * $loader->addClassMap([
 *  'name' => 'file'
 * ]);
 * ```
 */
class AutoLoader
{
    /**
     * @var self
     */
    private static $loader;

    /**
     * @var array
     */
    private static $files = [];

    /**
     * @var array
     * array (
     *  'prefix' => 'dir path'
     * )
     */
    private $psr0Map = [];

    /**
     * @var array
     * array (
     *  'prefix' => 'dir path'
     * )
     */
    private $psr4Map = [];

    /**
     * @var array
     */
    private $classMap = [];

    /**
     * @var array
     */
    private $missingClasses = [];

    /**
     * @param array $files
     *
     * @return self
     */
    public static function getLoader(array $files = []): self
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        if ($files) {
            self::addFiles($files);
        }

        self::$loader = $loader = new self();

        $loader->register(true);

        foreach (self::$files as $fileIdentifier => $file) {
            _globalIncludeFile($fileIdentifier, $file);
        }

        return $loader;
    }

    /**************************************************************************
     * independent files
     *************************************************************************/

    /**
     * @return array
     */
    public static function getFiles(): array
    {
        return self::$files;
    }

    /**
     * @param array $files
     */
    public static function setFiles(array $files): void
    {
        self::$files = $files;
    }

    /**
     * @param array $files
     */
    public static function addFiles(array $files): void
    {
        if (self::$files) {
            self::$files = array_merge(self::$files, $files);
        } else {
            self::$files = $files;
        }
    }

    /**************************************************************************
     * class loader
     *************************************************************************/

    /**
     * @param string $prefix
     * @param string $path
     */
    public function addPsr0(string $prefix, string $path): void
    {
        $this->psr0Map[$prefix] = $path;
    }

    /**
     * @param array $psr0Map Class to filename map
     */
    public function addPsr0Map(array $psr0Map): void
    {
        if ($this->psr0Map) {
            $this->psr0Map = array_merge($this->psr0Map, $psr0Map);
        } else {
            $this->psr0Map = $psr0Map;
        }
    }

    /**
     * @param string $prefix
     * @param string $path
     *
     * @throws InvalidArgumentException
     */
    public function addPsr4(string $prefix, string $path): void
    {
        // Register directories for a new namespace.
        $length = strlen($prefix);

        if ('\\' !== $prefix[$length - 1]) {
            throw new InvalidArgumentException('A non-empty PSR-4 prefix must end with a namespace separator.');
        }

        $this->psr4Map[$prefix] = $path;
    }

    /**
     * @param array $psr4Map Class to filename map
     */
    public function addPsr4Map(array $psr4Map): void
    {
        if ($this->psr4Map) {
            $this->psr4Map = array_merge($this->psr4Map, $psr4Map);
        } else {
            $this->psr4Map = $psr4Map;
        }
    }

    /**
     * @return array
     */
    public function getPsr4Map(): array
    {
        return $this->psr4Map;
    }

    /**
     * @param array $psr4Map
     */
    public function setPsr4Map(array $psr4Map): void
    {
        $this->psr4Map = $psr4Map;
    }

    /**
     * @return array
     */
    public function getClassMap(): array
    {
        return $this->classMap;
    }

    /**
     * @param array $classMap
     */
    public function setClassMap(array $classMap): void
    {
        $this->classMap = $classMap;
    }

    /**
     * @param array $classMap Class to filename map
     */
    public function addClassMap(array $classMap): void
    {
        if ($this->classMap) {
            $this->classMap = array_merge($this->classMap, $classMap);
        } else {
            $this->classMap = $classMap;
        }
    }

    /**
     * Registers this instance as an autoloader.
     *
     * @param bool $prepend Whether to prepend the autoloader or not
     */
    public function register(bool $prepend = false): void
    {
        spl_autoload_register([$this, 'loadClass'], true, $prepend);
    }

    /**
     * Un-registers this instance as an autoloader.
     */
    public function unRegister(): void
    {
        spl_autoload_unregister([$this, 'loadClass']);
    }

    /**
     * Loads the given class or interface.
     *
     * @param string $class The name of the class
     *
     * @return bool|null True if loaded, null otherwise
     */
    public function loadClass(string $class): ?bool
    {
        if ($file = $this->findFile($class)) {
            _includeClassFile($file);
            return true;
        }

        return null;
    }

    /**
     * Finds the path to the file where the class is defined.
     *
     * @param string $class The name of the class
     *
     * @return string|false The path if found, false otherwise
     */
    public function findFile(string $class)
    {
        // work around for PHP 5.3.0 - 5.3.2 https://bugs.php.net/50731
        if ('\\' === $class[0]) {
            $class = (string)substr($class, 1);
        }

        // class map lookup
        if (isset($this->classMap[$class])) {
            return $this->classMap[$class];
        }

        $file = $this->findFileWithExtension($class);

        if (false === $file) {
            // Remember that this class does not exist.
            $this->missingClasses[$class] = true;
        }

        return $file;
    }

    /**
     * @param string $class
     *
     * @return bool|string
     */
    private function findFileWithExtension(string $class)
    {
        // PSR-4 lookup
        $logicalPathPsr4 = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';

        // PSR-4
        foreach ($this->psr4Map as $prefix => $dir) {
            if (str_starts_with($class, $prefix)) {
                $length = strlen($prefix);

                if (file_exists($file = $dir . DIRECTORY_SEPARATOR . substr($logicalPathPsr4, $length))) {
                    return $file;
                }
            }
        }

        // PEAR-like class name
        $logicalPathPsr0 = str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';

        foreach ($this->psr0Map as $prefix => $dir) {
            if (str_starts_with($class, $prefix)) {
                $file = $dir . DIRECTORY_SEPARATOR . $logicalPathPsr0;

                if (file_exists($file)) {
                    return $file;
                }
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public function getMissingClasses(): array
    {
        return $this->missingClasses;
    }
}

function _globalIncludeFile($fileIdentifier, $file): void
{
    if (empty($GLOBALS['__global_autoload_files'][$fileIdentifier])) {
        /** @noinspection PhpIncludeInspection */
        require $file;

        $GLOBALS['__global_autoload_files'][$fileIdentifier] = true;
    }
}

/**
 * Scope isolated include.
 * Prevents access to $this/self from included files.
 *
 * @param $file
 */
function _includeClassFile($file): void
{
    /** @noinspection PhpIncludeInspection */
    include $file;
}
