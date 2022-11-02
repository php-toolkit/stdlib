<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Str;

use Toolkit\Stdlib\Str;
use function in_array;
use function trim;

/**
 * class StrValue
 */
class StrValue
{
    /**
     * @var string
     */
    protected string $value = '';

    /**
     * @param string $str
     *
     * @return self
     */
    public static function new(string $str): self
    {
        return new self($str);
    }

    /**
     * @param string $str
     *
     * @return self
     */
    public static function newTrim(string $str): self
    {
        return new self(trim($str));
    }

    /**
     * Class constructor.
     *
     * @param string $str
     */
    public function __construct(string $str)
    {
        $this->value = $str;
    }

    public function reset(): void
    {
        $this->value = '';
    }

    /**
     * @param string $content
     */
    public function append(string $content): void
    {
        $this->value .= $content;
    }

    /**
     * @param string ...$strings
     */
    public function appends(string ...$strings): void
    {
        foreach ($strings as $str) {
            $this->value .= $str;
        }
    }

    /**
     * @param string $content
     */
    public function prepend(string $content): void
    {
        $this->value = $content . $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->value !== '';
    }

    /**
     * @return int
     */
    public function length(): int
    {
        return Str::strlen($this->value);
    }

    // ------------------ process ------------------

    /**
     * @param callable(string): string $fn
     *
     * @return $this
     */
    public function apply(callable $fn): self
    {
        $this->value = $fn($this->value);
        return $this;
    }

    /**
     * @param string $chars
     *
     * @return $this
     */
    public function trim(string $chars = " \t\n\r\0\x0B"): self
    {
        if ($this->value) {
            $this->value = trim($this->value, $chars);
        }

        return $this;
    }

    /**
     * @return StrValue
     */
    public function trimmed(): self
    {
        return self::newTrim($this->value);
    }

    // ------------------ check ------------------

    /**
     * @param callable(string): bool $fn
     *
     * @return bool
     */
    public function is(callable $fn): bool
    {
        return (bool)$fn($this->value);
    }

    /**
     * @param string $sub
     *
     * @return bool
     */
    public function contains(string $sub): bool
    {
        return Str::strpos($this->value, $sub) !== false;
    }

    /**
     * @param string $prefix
     *
     * @return bool
     */
    public function hasPrefix(string $prefix): bool
    {
        return Str::hasPrefix($this->value, $prefix);
    }

    /**
     * @param string $suffix
     *
     * @return bool
     */
    public function hasSuffix(string $suffix): bool
    {
        return Str::hasSuffix($this->value, $suffix);
    }

    /**
     * @param string $str
     *
     * @return bool
     */
    public function isSubstrOf(string $str): bool
    {
        return Str::strpos($str, $this->value) !== false;
    }

    /**
     * @param string[] $arr
     *
     * @return bool
     */
    public function isOneOf(array $arr): bool
    {
        return in_array($this->value, $arr, true);
    }

    // ------------------ convert ------------------

    /**
     * @return int
     */
    public function int(): int
    {
        return (int)$this->value;
    }

    /**
     * @return int
     */
    public function toInt(): int
    {
        return (int)$this->value;
    }

    /**
     * @return int
     */
    public function getInt(): int
    {
        return (int)$this->value;
    }

    /**
     * @return int[]
     */
    public function toInts(string $sep = ','): array
    {
        return Str::str2ints($this->value, $sep);
    }

    /**
     * @return int[]
     */
    public function getInts(string $sep = ','): array
    {
        return $this->toInts($sep);
    }

    /**
     * @return bool
     */
    public function bool(): bool
    {
        return $this->getBool();
    }

    /**
     * @return bool
     */
    public function toBool(): bool
    {
        return $this->getBool();
    }

    /**
     * @return bool
     */
    public function getBool(): bool
    {
        return Str::toBool($this->value);
    }

    /**
     * @return string[]
     */
    public function toArray(string $sep = ',', int $limit = 0): array
    {
        return $this->toStrings($sep, $limit);
    }

    /**
     * @return string[]
     */
    public function toStrings(string $sep = ',', int $limit = 0): array
    {
        return Str::explode($this->value, $sep, $limit);
    }

    /**
     * @return string[]
     */
    public function getStrings(string $sep = ',', int $limit = 0): array
    {
        return $this->toStrings($sep, $limit);
    }

    /**
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->getValue();
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }
}
