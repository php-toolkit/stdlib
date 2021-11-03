<?php declare(strict_types=1);

namespace Toolkit\Stdlib\Std;

use Throwable;

/**
 * class StrStream - String value wrapper
 */
class StrStream
{
    /**
     * @var string
     */
    protected $str = '';

    /**
     * @param string $str
     *
     * @return static
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
        $this->str = $str;
    }

    /**
     * @param string $str
     *
     * @return $this
     */
    public function set(string $str): self
    {
        $this->str = $str;
        return $this;
    }

    /**
     * @param callable(string): string $fn
     *
     * @return $this
     */
    public function apply(callable $fn): self
    {
        return self::new($fn($this->str));
    }

    /**
     * @param callable(string): bool $fn
     *
     * @return bool
     */
    public function is(callable $fn): bool
    {
        return (bool)$fn($this->str);
    }

    /**
     * @param callable(string): string $fn
     *
     * @return $this
     */
    public function applyIf(callable $fn, $ifExpr): self
    {
        if ($ifExpr) {
            return self::new($fn($this->str));
        }

        return $this;
    }

    /**
     * @param string $str
     *
     * @return string
     */
    public function getOrNew(string $str): string
    {
        return $this->isEmpty() ? $str : $this->str;
    }

    /**
     * @param Throwable $e
     *
     * @return string
     * @throws Throwable
     */
    public function getOrThrow(Throwable $e): string
    {
        if ($this->isEmpty()) {
            throw $e;
        }

        return $this->str;
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->str === '';
    }

    /**
     * @return StrValue
     */
    public function getValue(): StrValue
    {
        return StrValue::new($this->str);
    }

    /**
     * @return StrValue
     */
    public function toValue(): StrValue
    {
        return StrValue::new($this->str);
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->str;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->str;
    }
}
