<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Str;

use Stringable;
use Toolkit\Stdlib\Helper\DataHelper;
use function array_unshift;
use function implode;
use function sprintf;

/**
 * Class StrBuffer
 *
 * @package Toolkit\Stdlib\Str
 */
class StrBuffer implements Stringable
{
    /**
     * @var self|null
     */
    private static ?StrBuffer $global = null;

    /**
     * @var string[]
     */
    private array $parts = [];

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
     *
     * @return self
     */
    public function write(string $content): self
    {
        $this->parts[] = $content;
        return $this;
    }

    /**
     * @param string $fmt
     * @param mixed  ...$args
     *
     * @return self
     */
    public function writef(string $fmt, ...$args): self
    {
        $this->parts[] = sprintf($fmt, ...$args);
        return $this;
    }

    /**
     * @param string $content
     *
     * @return self
     */
    public function writeln(string $content): self
    {
        $this->parts[] = $content . "\n";
        return $this;
    }

    /**
     * @param mixed $content
     *
     * @return self
     */
    public function writeAny(mixed $content): self
    {
        $this->parts[] = DataHelper::toString($content);
        return $this;
    }

    /**
     * @param string $content
     *
     * @return self
     */
    public function append(string $content): self
    {
        $this->write($content);
        return $this;
    }

    /**
     * @param string ...$contents
     *
     * @return self
     */
    public function appends(string ...$contents): self
    {
        foreach ($contents as $content) {
            $this->parts[] = $content;
        }
        return $this;
    }

    /**
     * @param string $content
     *
     * @return self
     */
    public function prepend(string $content): self
    {
        array_unshift($this->parts, $content);
        return $this;
    }

    /**
     * @param string ...$contents
     *
     * @return self
     */
    public function prepends(string ...$contents): self
    {
        array_unshift($this->parts, ...$contents);
        return $this;
    }

    public function reset(): void
    {
        $this->parts = [];
    }

    /**
     * Get and clear data
     *
     * @return string
     */
    public function fetch(): string
    {
        $strings = $this->parts;
        // clear
        $this->parts = [];

        return implode('', $strings);
    }

    /**
     * Get and clear data
     *
     * @return string
     */
    public function getAndClear(): string
    {
        return $this->fetch();
    }

    /**
     * clear data
     */
    public function clear(): string
    {
        return $this->getAndClear();
    }

    /**
     * @return string[]
     */
    public function getParts(): array
    {
        return $this->parts;
    }

    /**
     * @param string $sep
     *
     * @return string
     */
    public function join(string $sep): string
    {
        return implode($sep, $this->parts);
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return implode('', $this->parts);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }
}
