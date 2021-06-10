<?php declare(strict_types=1);

namespace Toolkit\Stdlib\Str;

use Toolkit\Stdlib\Str;
use function trim;

/**
 * Class StrObject
 *
 * @package Toolkit\Stdlib\Str
 */
class StrObject
{
    /**
     * @var self
     */
    private static $global;

    /**
     * @var string
     */
    private $string;

    /**
     * @param string $str
     *
     * @return self
     */
    public static function global(string $str): self
    {
        if (!self::$global) {
            self::$global = new self($str);
        }

        return self::$global;
    }

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
     * Class constructor.
     *
     * @param string $str
     */
    public function __construct(string $str)
    {
        $this->string = $str;
    }

    public function reset(): void
    {
        $this->string = '';
    }

    /**
     * @param string $content
     */
    public function append(string $content): void
    {
        $this->string .= $content;
    }

    /**
     * @param string $content
     */
    public function prepend(string $content): void
    {
        $this->string = $content . $this->string;
    }

    /**
     * @param string $string
     */
    public function setString(string $string): void
    {
        $this->string = $string;
    }

    /**
     * @param string $chars
     *
     * @return $this
     */
    public function trim(string $chars = " \t\n\r\0\x0B"): self
    {
        if ($this->string) {
            $this->string = trim($this->string, $chars);
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->string !== '';
    }

    /**
     * @return int
     */
    public function length(): int
    {
        return Str::strlen($this->string);
    }

    /**
     * @return string
     */
    public function trimmed(): string
    {
        return trim($this->string);
    }

    // ------------------ check ------------------

    /**
     * @param string $sub
     *
     * @return bool
     */
    public function contains(string $sub): bool
    {
        return Str::strpos($this->string, $sub) !== false;
    }

    /**
     * @param string $prefix
     *
     * @return bool
     */
    public function hasPrefix(string $prefix): bool
    {
        return Str::hasPrefix($this->string, $prefix);
    }

    /**
     * @param string $suffix
     *
     * @return bool
     */
    public function hasSuffix(string $suffix): bool
    {
        return Str::hasSuffix($this->string, $suffix);
    }

    // ------------------ convert ------------------

    /**
     * @return int
     */
    public function toInt(): int
    {
        return (int)$this->string;
    }

    /**
     * @return int
     */
    public function getInt(): int
    {
        return (int)$this->string;
    }

    /**
     * @return int[]
     */
    public function toInts(string $sep = ','): array
    {
        return Str::str2ints($this->string, $sep);
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
    public function toBool(): bool
    {
        return Str::toBool($this->string);
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
        return Str::explode($this->string, $sep, $limit);
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
    public function toString(): string
    {
        return $this->string;
    }

    /**
     * @return string
     */
    public function getString(): string
    {
        return $this->string;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->string;
    }

}
