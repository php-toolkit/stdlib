<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Util\Stream;

use function implode;

/**
 * class StringStream
 */
class StringStream extends ListStream
{
    /**
     * @param mixed $value
     */
    public function append(mixed $value): void
    {
        parent::append((string)$value);
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
}
