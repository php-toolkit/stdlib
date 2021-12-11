<?php declare(strict_types=1);

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
