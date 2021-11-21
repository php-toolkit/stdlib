<?php declare(strict_types=1);

namespace Toolkit\Stdlib\Util\Stream;

use function implode;

/**
 * class StringsStream
 */
class StringsStream extends DataStream
{
    /**
     * @param string[] $strings
     *
     * @return static
     */
    public static function new(array $strings): self
    {
        return new self($strings);
    }

    /**
     * @param callable(string): string $func
     * @param bool|mixed $apply
     *
     * @return $this
     */
    public function eachIf(callable $func, mixed $apply): self
    {
        if (!$apply) {
            return $this;
        }

        return $this->each($func);
    }

    /**
     * @param callable(string): string $func
     *
     * @return $this
     */
    public function each(callable $func): self
    {
        $new = new self();
        foreach ($this as $str) {
            $new->append($func($str));
        }

        return $new;
    }

    /**
     * @param string $sep
     *
     * @return string
     */
    public function join(string $sep = ','): string
    {
        return $this->implode($sep);
    }

    /**
     * @param string $sep
     *
     * @return string
     */
    public function implode(string $sep = ','): string
    {
        return implode($sep, $this->getArrayCopy());
    }

    // public function prepend(string $value): self
    // {
    //     return $this;
    // }

    public function append($value): self
    {
        parent::append((string)$value);
        return $this;
    }
}
