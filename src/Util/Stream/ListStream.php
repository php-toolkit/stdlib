<?php declare(strict_types=1);

namespace Toolkit\Stdlib\Util\Stream;

/**
 * class ListStream
 *
 * @author inhere
 */
class ListStream extends DataStream
{
    /**
     * @param mixed $value
     *
     * @return $this
     */
    public function append(mixed $value): static
    {
        parent::append($value);
        return $this;
    }
}
