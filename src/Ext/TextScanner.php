<?php declare(strict_types=1);

namespace Toolkit\Stdlib\Ext;

use Iterator;
use Toolkit\Stdlib\Helper\Assert;
use function strtok;

/**
 * @author inhere
 */
class TextScanner implements Iterator
{
    /** @var string source content */
    private string $source;

    /**
     * @var string split token
     */
    private string $splitToken = "\n";

    private int $index = 0;
    private bool $start = false;
    private bool $done = false;

    /**
     * @var string current token text
     */
    private string $tokText = '';

    /**
     * @param string $source
     *
     * @return static
     */
    public static function new(string $source = ''): static
    {
        return new static($source);
    }

    public function __construct(string $source = '')
    {
        $this->source = $source;
    }

    /**
     * scan text token
     *
     * Usage:
     *
     * ```php
     * $s = Scanner::new($source);
     * while ($s->scan()) {
     *      $txt = $s->getText();
     *     // do something
     * }
     * ```
     *
     * @return bool
     */
    public function scan(): bool
    {
        if ($this->done) {
            return false;
        }

        if ($this->start) {
            $txt = strtok($this->splitToken);
        } else {
            $this->start = true;
            Assert::notEmpty($this->source, 'The source can not be empty');
            $txt = strtok($this->source, $this->splitToken);
        }

        // end
        if ($txt === false) {
            $this->tokText = '';
            // reset
            strtok('', '');
            $this->done = true;
            return false;
        }

        $this->index++;
        $this->tokText = $txt;
        return true;
    }

    /**
     * @return array = [bool, string]
     */
    public function nextText(): array
    {
        $ok = $this->scan();
        return [$ok, $this->tokText];
    }

    /**
     * find next token text from given token
     *
     * @return array = [bool, string]
     */
    public function nextToken(string $tok): array
    {
        $txt = strtok($tok);
        if ($txt !== false) {
            return [true, $txt];
        }
        return [false, ''];
    }

    /**
     * @return string get current token text
     */
    public function getText(): string
    {
        return $this->tokText;
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function setSource(string $source): void
    {
        $this->source = $source;
    }

    public function setSplitToken(string $splitToken): void
    {
        $this->splitToken = $splitToken;
    }

    public function current(): string
    {
        return $this->tokText;
    }

    public function next(): void
    {
        $this->scan();
    }

    public function key(): int
    {
        return $this->index;
    }

    public function valid(): bool
    {
        return !$this->done;
    }

    public function rewind(): void
    {
        $this->source  = '';
        $this->tokText = '';

        $this->index = 0;
        $this->start = $this->done = false;
    }

}