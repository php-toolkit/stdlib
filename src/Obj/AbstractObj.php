<?php declare(strict_types=1);

namespace Toolkit\Stdlib\Obj;

use Toolkit\Stdlib\Obj;
use Toolkit\Stdlib\Obj\Traits\QuickInitTrait;
use function get_object_vars;

/**
 * Class AbstractObj
 *
 * @package Toolkit\Stdlib\Obj
 */
abstract class AbstractObj
{
    use QuickInitTrait;

    /**
     * Class constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        Obj::init($this, $config);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
