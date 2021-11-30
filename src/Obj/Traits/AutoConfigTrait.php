<?php declare(strict_types=1);

namespace Toolkit\Stdlib\Obj\Traits;

use Toolkit\Stdlib\Obj;

/**
 * trait AutoConfigTrait
 *
 * @author inhere
 */
trait AutoConfigTrait
{
    /**
     * @param array $config
     *
     * @return static
     */
    public static function new(array $config = []): static
    {
        return new static($config);
    }

    /**
     * Class constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        Obj::init($this, $config);
    }
}
