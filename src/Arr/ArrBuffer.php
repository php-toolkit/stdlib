<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Arr;

/**
 * Class ArrBuffer
 *
 * @package Toolkit\Stdlib\Arr
 */
final class ArrBuffer
{
    /**
     * @var string[]
     */
    private array $body = [];

    /**
     * @var string
     */
    private string $delimiter = ''; // '/' ':'

    /**
     * @param string $first
     *
     * @return ArrBuffer
     */
    public static function new(string $first = ''): ArrBuffer
    {
        return new self($first);
    }

    /**
     * constructor.
     *
     * @param string $content
     */
    public function __construct(string $content = '')
    {
        if ($content) {
            $this->body[] = $content;
        }
    }

    /**
     * @param string $content
     */
    public function write(string $content): void
    {
        $this->body[] = $content;
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
        array_unshift($this->body, $content);
    }

    /**
     * clear
     */
    public function clear(): void
    {
        $this->body = [];
    }

    /**
     * @return string[]
     */
    public function getBody(): array
    {
        return $this->body;
    }

    /**
     * @param string[] $body
     */
    public function setBody(array $body): void
    {
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return implode($this->delimiter, $this->body);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * @return string
     */
    public function getDelimiter(): string
    {
        return $this->delimiter;
    }

    /**
     * @param string $delimiter
     */
    public function setDelimiter(string $delimiter): void
    {
        $this->delimiter = $delimiter;
    }
}
