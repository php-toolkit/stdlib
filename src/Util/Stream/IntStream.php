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
     *
     * @return $this
     */
    public function append(mixed $value): static
    {
        parent::append((int)$value);
        return $this;
    }
}
