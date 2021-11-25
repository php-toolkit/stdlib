<?php declare(strict_types=1);

namespace Toolkit\Stdlib\Util\Stream;

use function implode;

/**
 * class ListStream
 */
class ListStream extends DataStream
{
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

    public function append($value): static
    {
        parent::append($value);
        return $this;
    }
}
