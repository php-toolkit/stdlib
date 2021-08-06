<?php declare(strict_types=1);

namespace Toolkit\Stdlib\Obj;

use Toolkit\Stdlib\Obj;
use Toolkit\Stdlib\Obj\Traits\QuickInitTrait;

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
}
