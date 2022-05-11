<?php declare(strict_types=1);
/**
 * This file is part of toolkit/stdlib.
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/php-toolkit/stdlib
 * @license  MIT
 */

namespace Toolkit\Stdlib\Util\Stream;

/**
 * class IntStream
 *
 * @author inhere
 */
class IntStream extends ListStream
{
    /**
     * @param numeric $value
     */
    public function append(mixed $value): void
    {
        parent::append((int)$value);
    }
}
