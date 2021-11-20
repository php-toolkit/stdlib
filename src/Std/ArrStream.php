<?php declare(strict_types=1);

namespace Toolkit\Stdlib\Std;

/**
 * class ArrStream
 *
 * @author inhere
 */
class ArrStream
{
    /**
     * @param array $data
     *
     * @return static
     */
    public static function new(array $data = []): self
    {
        return new static($data);
    }


}
