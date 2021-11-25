<?php declare(strict_types=1);

namespace Toolkit\Stdlib\Util\Stream;

use function implode;

/**
 * class StringStream
 */
class StringStream extends ListStream
{
    /**
     * @param mixed $value
     *
     * @return $this
     */
    public function append(mixed $value): static
    {
        parent::append((string)$value);
        return $this;
    }
}
