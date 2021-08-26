<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Str;

use function array_unshift;
use function implode;
use function sprintf;

/**
 * Class StrBuffer
 *
 * @package Toolkit\Stdlib\Str
 */
class StrBuffer
{
    /**
     * @var self
     */
    private static $global;

    /**
     * @var string[]
     */
    private $parts = [];

    /**
     * @param string $str
     *
     * @return self
     */
    public static function global(string $str = ''): self
    {
        if (!self::$global) {
            self::$global = new self($str);
        }

        return self::$global;
    }

    /**
     * @param string $str
     *
     * @return static
     */
    public static function new(string $str = ''): self
    {
        return new self($str);
    }

    /**
     * Class constructor.
     *
     * @param string $str
     */
    public function __construct(string $str = '')
    {
        $this->write($str);
    }

    /**
     * @param string $content
     */
    public function write(string $content): void
    {
        $this->parts[] = $content;
    }

    /**
     * @param string $fmt
     * @param mixed  ...$args
     */
    public function writef(string $fmt, ...$args): void
    {
        $this->parts[] = sprintf($fmt, ...$args);
    }

    /**
     * @param string $content
     */
    public function writeln(string $content): void
    {
        $this->parts[] = $content . "\n";
    }

    /**
     * @param string $content
     */
    public function append(string $content): void
    {
        $this->write($content);
    }

    /**
     * @param string $content
     */
    public function prepend(string $content): void
    {
        array_unshift($this->parts, $content);
    }

    public function reset(): void
    {
        $this->parts = [];
    }

    /**
     * clear data
     */
    public function clear(): string
    {
        $strings = $this->parts;
        // clear
        $this->parts = [];

        return implode($strings);
    }

    /**
     * @return string[]
     */
    public function getParts(): array
    {
        return $this->parts;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return implode($this->parts);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }
}
